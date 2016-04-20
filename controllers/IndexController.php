<?php

namespace Letoh\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class IndexController {

	public function indexAction(Request $req, Application $app) {
        $sql = "SELECT COUNT(*) AS hotelCount FROM Hotel";
        $hotelCount = $app['db']->fetchAssoc($sql)['hotelCount'];

        $sql = "SELECT COUNT(*) AS hotelRoomCount FROM HotelRoom";
        $hotelRoomCount = $app['db']->fetchAssoc($sql)['hotelRoomCount'];

        $sql = "SELECT COUNT(*) AS customerCount FROM Customer";
        $customerCount = $app['db']->fetchAssoc($sql)['customerCount'];

		return $app['twig']->render('home.twig', array(
            'hotelCount' => $hotelCount,
            'hotelRoomCount' => $hotelRoomCount,
            'customerCount' => $customerCount));
	}

}