<?php
/**
 * Created by PhpStorm.
 * User: mbrochard
 * Date: 13/04/2016
 * Time: 13:25
 */

namespace Letoh\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AccountController {

    public function indexAction(Request $req, Application $app) {
        return $app['twig']->render('account.twig');
    }

}