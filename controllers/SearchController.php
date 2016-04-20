<?php

namespace Letoh\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class SearchController {

	public function indexAction(Request $req, Application $app) {
        $town = strtoupper($req->get('town'));
        $fromDate = $req->get('from');
        $toDate = $req->get('to');

        // Transformation des dates
        $fromDate = str_replace('/', '-', $fromDate);
        $toDate = str_replace('/', '-', $toDate);
        $fromDate = strtotime($fromDate);
        $toDate = strtotime($toDate);

        $fromDate = date('Y-m-d', $fromDate);
        $toDate = date('Y-m-d', $toDate);

        $hotels = $app['db']->createQueryBuilder()
            ->select('h.id, h.name, h.rating, COUNT(hr.idHotel) as hotelRoomCount, MIN(hr.price) as minPrice')
            ->from('Hotel', 'h')
            ->join('h', 'Town', 't', 'h.idTown = t.id')
            ->join('h', 'HotelRoom', 'hr', 'hr.idHotel = h.id')
            ->where('t.name = :townName')
            ->groupBy('hr.idHotel')
            ->setParameter(':townName', $town)
            ->execute()
            ->fetchAll();

        // Recherche le prix le plus bas de tous les hôtels
        $allMinPrice = PHP_INT_MAX;
        foreach ($hotels as $hotel) {
            $allMinPrice = min($allMinPrice, $hotel['minPrice']);
        }

        // Recherche le prix le plus haut de tous les hôtels
        $allMaxPrice = 0;
        foreach ($hotels as $hotel) {
            $allMaxPrice = max($allMaxPrice, $hotel['minPrice']);
        }

        return $app['twig']->render('search.twig', array(
            'town' => $town,
            'hotels' => $hotels,
            'allMinPrice' => $allMinPrice,
            'allMaxPrice' => $allMaxPrice,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ));
	}

}

?>