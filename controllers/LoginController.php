<?php

namespace Letoh\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class LoginController {

    public function indexAction(Request $req, Application $app) {
        return $app['twig']->render('login.twig', array(
            'error' => $app['translator']->trans($app['security.last_error']($req), array()),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

}

?>