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

        // Récupération des filtres (si donnés)
        $minRating = 1;
        $maxRating = 5;
        $minPrice = 0;
        $maxPrice = PHP_INT_MAX;

        if ($req->get('minRating')) {
            $minRating = $req->get('minRating');
        } if ($req->get('maxRating')) {
            $maxRating = $req->get('maxRating');
        } if ($req->get('minPrice')) {
            $minPrice = $req->get('minPrice');
        } if ($req->get('maxPrice')) {
            $maxPrice = $req->get('maxPrice');
        }

        $sql = "SELECT h.id, h.name, h.rating, COUNT(hr.idHotel) as hotelRoomCount, MIN(hr.price) as minPrice, MIN(h.rating) as minRating, MAX(h.rating) as maxRating ";
        $sql .= "FROM Hotel h ";
        $sql .= "JOIN Town t ON h.idTown = t.id ";
        $sql .= "JOIN HotelRoom hr ON hr.idHotel = h.id ";
        $sql .= "WHERE t.name = :townName AND ";
        $sql .= "h.rating >= :minRating AND ";
        $sql .= "h.rating <= :maxRating AND ";
        $sql .= "hr.price >= :minPrice AND ";
        $sql .= "hr.price <= :maxPrice ";
        if ($req->get('privative') AND $req->get('dortoir')) {
            $sql .= "AND (hr.type = 0 OR hr.type = 1)";
        } else {
            if ($req->get('privative')) {
                $sql .= "AND hr.type = 0 ";
            } if ($req->get('dortoir')) {
                $sql .= "AND hr.type = 1 ";
            }
        }
        $sql .= "GROUP BY h.id";

        $stmt = $app['db']->prepare($sql);
        $stmt->bindValue("townName", $town);
        $stmt->bindValue("minRating", $minRating);
        $stmt->bindValue("maxRating", $maxRating);
        $stmt->bindValue("minPrice", $minPrice);
        $stmt->bindValue("maxPrice", $maxPrice);
        $stmt->execute();

        $hotels = $stmt->fetchAll();

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