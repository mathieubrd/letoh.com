<?php

namespace Letoh\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 27/03/2016
 * Time: 13:19
 */
class HotelController {

    public function indexAction(Request $req, Application $app) {
        // Récupère les informations de l'hotel
        $sql = "
            SELECT h.name, h.rating, h.address, t.name AS town
            FROM Hotel h
            JOIN Town t ON h.idTown = t.id
            WHERE h.id = :idHotel";
        $stmt = $app['db']->prepare($sql);
        $stmt->bindValue('idHotel', $req->get('idHotel'));
        $stmt->execute();
        $hotel = $stmt->fetch();

        // Si l'hotel n'existe pas, redirige vers 404
        if (!$hotel) {
            return $app['twig']->render('404.twig');
        }

        // Récupère les chambres
        $sql = "SELECT h.rating, hr.capacity, hr.type, hr.price, hr.id
            FROM Hotel h
            JOIN HotelRoom hr ON hr.idHotel = h.id
            WHERE h.id = :idHotel AND
              (SELECT COUNT(b.id)
                FROM Booking b
                WHERE b.idHotelRoom = hr.id AND
                  b.arrival >= :arrival AND
                  b.departure <= :departure
              ) == 0";
        $stmt = $app['db']->prepare($sql);
        $stmt->bindValue('idHotel', $req->get('idHotel'));
        $stmt->bindValue('arrival', $req->get('arrival'));
        $stmt->bindValue('departure', $req->get('departure'));
        $stmt->execute();

        $rooms = $stmt->fetchAll();

        return $app['twig']->render('hotel.twig', array(
            'hotel' => $hotel,
            'rooms' => $rooms,
            'arrival' => $req->get('arrival'),
            'departure' => $req->get('departure')));
    }

}