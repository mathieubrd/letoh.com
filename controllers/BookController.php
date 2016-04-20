<?php
/**
 * Created by PhpStorm.
 * User: mbrochard
 * Date: 12/04/2016
 * Time: 10:57
 */

namespace Letoh\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class BookController {

    public function indexAction(Request $req, Application $app) {
        // Récupère les attributs de la requête
        $idRoom = $req->get('idRoom');
        $fromDate = $req->get('fromDate');
        $toDate = $req->get('toDate');

        // Calcul la différence en nombre de jours entre les deux dates
        $fromDate = date_create($fromDate);
        $toDate = date_create($toDate);
        $dateDiff = date_diff($fromDate, $toDate, true);

        // Récupère la chambre d'hôtel concernée
        $hotelRoom = $app['db']->createQueryBuilder()
            ->select('hr.capacity, hr.price, hr.id')
            ->from('HotelRoom', 'hr')
            ->where('hr.id = ?')
            ->setParameter(0, $idRoom)
            ->execute()
            ->fetch();

        // Si la chambre d'hôtel n'est pas trouvé, affiche une erreur 404
        if (!$hotelRoom) {
            return $app['twig']->render('404.twig');
        }

        // Calcul du prix total
        $totalPrice = $dateDiff->days * $hotelRoom['price'];

        // Affiche la vue
        return $app['twig']->render('book.twig', array(
            'hotelRoom' => $hotelRoom,
            'totalPrice' => $totalPrice,
            'fromDate' => $fromDate->format('d/m/Y'),
            'toDate' => $toDate->format('d/m/Y'),
        ));
    }

    public function confirmAction(Request $req, Application $app) {
        $idHotelRoom = $req->get('idHotelRoom');
        $arrival = $req->get('fromDate');
        $departure = $req->get('toDate');

        // Transformation des dates
        $arrival = str_replace('/', '-', $arrival);
        $departure = str_replace('/', '-', $departure);
        $arrival = strtotime($arrival);
        $departure = strtotime($departure);
        $arrival = date('Y-m-d', $arrival);
        $departure = date('Y-m-d', $departure);

        $user = $app['db']->createQueryBuilder()
            ->select('c.id')
            ->from('Customer', 'c')
            ->where('c.mail = ?')
            ->setParameter(0, $app['security.token_storage']->getToken()->getUser()->getUsername())
            ->execute()
            ->fetch();

        $app['db']->createQueryBuilder()
            ->insert('Booking')
            ->values(
                array(
                    'idHotelRoom' => '?',
                    'idCustomer' => '?',
                    'arrival' => '?',
                    'departure' => '?',
                )
            )
            ->setParameter(0, $idHotelRoom)
            ->setParameter(1, $user['id'])
            ->setParameter(2, $arrival)
            ->setParameter(3, $departure)
            ->execute();

        return $app['twig']->render('book_confirm.twig');
    }

}