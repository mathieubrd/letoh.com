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
            ->select('hr.capacity, hr.price')
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
        ));
    }

}