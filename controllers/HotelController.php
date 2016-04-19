<?php

namespace Letoh\Controller;

require_once 'model/Hotel.php';
require_once 'model/HotelRoom.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Letoh\Model\Hotel;
use Letoh\Model\HotelRoom;

/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 27/03/2016
 * Time: 13:19
 */
class HotelController {

    public function indexAction(Request $req, Application $app) {
        // Récupère de l'hotel
        $qb = $app['db']->createQueryBuilder();
        $qb
            ->select('h.*', 't.name AS townName')
            ->from('Hotel', 'h')
            ->join('h', 'Town', 't', 'h.idTown = t.id')
            ->where('h.id = ?')
            ->setParameter(0, $req->attributes->get('id'));
        $hotel = $qb->execute()->fetch();

        $townName = $hotel['townName'];

        // Si l'hotel n'existe pas, redirige vers 404
        if (!$hotel) {
            return $app['twig']->render('404.twig');
        }

        // Récupère les chambres disponibles de l'hotel
        $qb->resetQueryParts();
        $qb
            ->select('hr.*')
            ->from('HotelRoom', 'hr')
            ->where('hr.idHotel = ?')
            ->setParameter(0, $req->attributes->get('id'));
        $hotelRooms = $qb->execute()->fetchAll();

        return $app['twig']->render('hotel.twig', array(
            'hotel' => $hotel,
            'townName' => ucfirst(strtolower($townName)),
            'hotelRooms' => $hotelRooms));
    }

}