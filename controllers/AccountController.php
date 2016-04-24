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
        $mail = $token = $app['security.token_storage']->getToken()->getUser()->getUsername();

        // Récupère la liste des réservations du clients
        $booking = $app['db']->createQueryBuilder()
            ->select('b.id, b.arrival, b.departure, h.name AS hotelName, hr.price AS roomPrice')
            ->from('Booking', 'b')
            ->join('b', 'Customer', 'c', 'b.idCustomer = c.id')
            ->join('b', 'HotelRoom', 'hr', 'b.idHotelRoom = hr.id')
            ->join('hr', 'Hotel', 'h', 'hr.idHotel = h.id')
            ->where('c.mail = ?')
            ->setParameter(0, $mail)
            ->execute()
            ->fetchAll();

        // Calcul des prix
        $prices = [];
        foreach ($booking as $book) {
            $arrival = date_create($book['arrival']);
            $departure = date_create($book['departure']);
            $dateDiff = date_diff($departure, $arrival, true);
            array_push($prices, $dateDiff->days * $book['roomPrice']);
        }

        return $app['twig']->render('account.twig', array(
            'booking' => $booking,
            'prices' => $prices,
        ));
    }

}