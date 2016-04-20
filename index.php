<?php

require_once 'vendor/autoload.php';
require_once 'controllers/IndexController.php';
require_once 'controllers/SignupController.php';
require_once 'controllers/SearchController.php';
require_once 'controllers/LoginController.php';
require_once 'controllers/HotelController.php';
require_once 'controllers/BookController.php';
require_once 'controllers/AccountController.php';
require_once 'UserProvider.php';

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

$app->get('/', 'Letoh\Controller\IndexController::indexAction')->bind('home');
$app->post('/search', 'Letoh\Controller\SearchController::indexAction')->bind('search');
$app->get('/hotel/{id}', 'Letoh\Controller\HotelController::indexAction');
$app->match('/signup', 'Letoh\Controller\SignupController::indexAction')->bind('signup');
$app->get('/signup/success', 'Letoh\Controller\SignupController::successAction')->bind('signup_success');
$app->get('/login', 'Letoh\Controller\LoginController::indexAction')->bind('login');
$app->get('/book/{idRoom}/{fromDate}/{toDate}', 'Letoh\Controller\BookController::indexAction')->bind('book');
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

$app->run();