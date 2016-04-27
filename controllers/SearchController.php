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

        $hotelProvider = $app['hotel_provider'];
        $hotels = $hotelProvider->getData(
            $town,
            $fromDate,
            $toDate,
            $req->get('minRating'),
            $req->get('maxRating'),
            $req->get('minPrice'),
            $req->get('maxPrice'));

        if (count($hotels) > 0) {
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
        } else {
            $allMinPrice = 0;
            $allMaxPrice = 0;
        }

        if ($req->get('minPrice') == null) $minPrice = $allMinPrice;
        else $minPrice = $req->get('minPrice');

        if ($req->get('maxPrice') == null) $maxPrice = $allMaxPrice;
        else $maxPrice = $req->get('maxPrice');

        $opt = array(
            'town' => $town,
            'hotels' => $hotels,
            'allMinPrice' => $allMinPrice,
            'allMaxPrice' => $allMaxPrice,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        );

        if ($req->get('privative')) {
            $opt['privative'] = $req->get('privative');
        } if ($req->get('dortoir')) {
            $opt['dortoir'] = $req->get('dortoir');
        } if (!$req->get('privative') && !$req->get('dortoir')) {
            $opt['privative'] = 1;
        }

        return $app['twig']->render('search.twig', $opt);
	}

}

?>