<?php

require_once 'vendor/autoload.php';
require_once 'controllers/IndexController.php';
require_once 'controllers/SignupController.php';
require_once 'controllers/SearchController.php';
require_once 'controllers/LoginController.php';
require_once 'controllers/HotelController.php';
require_once 'controllers/BookController.php';
require_once 'controllers/AccountController.php';
require_once 'services/UserProvider.php';
require_once 'services/HotelProvider.php';

$app = new Silex\Application();

$app['debug'] = true;

// Plugins
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => 'views',
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => 'letoh.db',
    ),
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\HttpFragmentServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale' => 'fr',
));
$app['translator.domains'] = array(
    'messages' => array(
        'fr' => array(
            'Bad credentials.' => 'Identifiants incorrects.',
        )
    )
);
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secure' => array(
            'pattern' => '^/',
            'form' => array('login_path' => '/login', 'check_path' => '/account/login_check'),
            'logout' => array('logout_path' => '/account/logout', 'invalidate_session' => true),
            'users' => $app->share(function () use ($app) {
                return new UserProvider($app['db']);
            }),
            'anonymous' => true,
        ),
    )
));

$app['security.access_rules'] = array(
    array('^/account', 'ROLE_USER', null),
    array('^/book', 'ROLE_USER', null)
);

$app['hotel_provider'] = function($app) {
   return new HotelProvider($app['db']);
};

$app->get('/', 'Letoh\Controller\IndexController::indexAction')->bind('home');
$app->post('/search', 'Letoh\Controller\SearchController::indexAction')->bind('search');
$app->post('/hotel', 'Letoh\Controller\HotelController::indexAction')->bind('hotel');
$app->match('/signup', 'Letoh\Controller\SignupController::indexAction')->bind('signup');
$app->get('/signup/success', 'Letoh\Controller\SignupController::successAction')->bind('signup_success');
$app->get('/login', 'Letoh\Controller\LoginController::indexAction')->bind('login');
$app->post('/book', 'Letoh\Controller\BookController::indexAction')->bind('book');
$app->post('/book/confirm', 'Letoh\Controller\BookController::confirmAction')->bind('book_confirm');
$app->match('/account', 'Letoh\Controller\AccountController::indexAction')->bind('account');

/**
 * $app->error(function(\Exception $e, $code) use ($app) {
 * switch ($code) {
 * case 404:
 * return $app['twig']->render('404.twig');
 * default:
 * return $app['twig']->render('error.twig', array('errorCode' => $code));
 * }
 * });
 */

// Création du schéma de la base de données
$schema = new \Doctrine\DBAL\Schema\Schema();
$sm = $app['db']->getSchemaManager();

if (count($sm->listTableDetails('Hotel')->getColumns()) == 0) {
    $table = $schema->createTable('Hotel');
    $table->addColumn('id', 'integer', array('autoincrement' => true));
    $table->addColumn('idTown', 'integer', array('notnull' => true));
    $table->addColumn('name', 'string', array('notnull' => true));
    $table->addColumn('address', 'string', array('notnull' => true));
    $table->addColumn('rating', 'integer', array('notnull' => true));
    $table->setPrimaryKey(array('id'));
}
if (count($sm->listTableDetails('HotelRoom')->getColumns()) == 0) {
    $table = $schema->createTable('HotelRoom');
    $table->addColumn('id', 'integer', array('autoincrement' => true));
    $table->addColumn('idHotel', 'integer', array('notnull' => true));
    $table->addColumn('capacity', 'integer', array('notnull' => true));
    $table->addColumn('type', 'integer', array('notnull' => true));
    $table->addColumn('price', 'decimal', array('notnull' => true));
    $table->setPrimaryKey(array('id'));
}
if (count($sm->listTableDetails('Customer')->getColumns()) == 0) {
    $table = $schema->createTable('Customer');
    $table->addColumn('id', 'integer', array('autoincrement' => true));
    $table->addColumn('lastName', 'string', array('notnull' => true));
    $table->addColumn('firstName', 'string', array('notnull' => true));
    $table->addColumn('address', 'string', array('notnull' => true));
    $table->addColumn('phoneNumber', 'string', array('notnull' => true));
    $table->addColumn('mail', 'string', array('notnull' => true));
    $table->addColumn('password', 'string', array('notnull' => true));
    $table->addColumn('roles', 'string', array('notnull' => true, 'default' => 'ROLE_USER'));
    $table->setPrimaryKey(array('id'));
}
if (count($sm->listTableDetails('Town')->getColumns()) == 0) {
    $table = $schema->createTable('Town');
    $table->addColumn('id', 'integer', array('autoincrement' => true));
    $table->addColumn('name', 'string', array('notnull' => true));
    $table->addColumn('latitude', 'string', array('notnull' => true));
    $table->addColumn('longitude', 'string', array('notnull' => true));
    $table->setPrimaryKey(array('id'));
}
if (count($sm->listTableDetails('Booking')->getColumns()) == 0) {
    $table = $schema->createTable('Booking');
    $table->addColumn('id', 'integer', array('autoincrement' => true));
    $table->addColumn('idHotelRoom', 'integer', array('notnull' => true));
    $table->addColumn('idCustomer', 'integer', array('notnull' => true));
    $table->addColumn('arrival', 'string', array('notnull' => true));
    $table->addColumn('departure', 'string', array('notnull' => true));
    $table->setPrimaryKey(array('id'));
}

$queries = $schema->toSql($app['db']->getDatabasePlatform());

foreach ($queries as $query) {
    $app['db']->query($query);
}

// Modifications droits base de données
chmod('letoh.db', 0764);

$app->run();