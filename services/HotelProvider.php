<?php

/**
 * Service qui retourne une liste d'hôtels et leur(s) chambre(s) disponible(s) en fonciton des filtres donnés
 */
class HotelProvider {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getData($town, $arrival, $departure, $minRating = null, $maxRating = null, $minRoomPrice = null, $maxRoomPrice = null, $roomType = null) {
        if ($minRating == null) $minRating = 0;
        if ($maxRating == null) $maxRating = 5;
        if ($minRoomPrice == null) $minRoomPrice = 0;
        if ($maxRoomPrice == null) $maxRoomPrice = PHP_INT_MAX;
        if ($roomType == null) $roomType = 0;

        $sql = "SELECT h.id, h.name, h.rating, t.name as town, COUNT(hr.idHotel) as hotelRoomCount, MIN(hr.price) as minPrice, MIN(h.rating) as minRating, MAX(h.rating) as maxRating ";
        $sql .= "FROM Hotel h ";
        $sql .= "JOIN Town t ON h.idTown = t.id ";
        $sql .= "JOIN HotelRoom hr ON hr.idHotel = h.id ";
        $sql .= "WHERE t.name = :townName AND ";
        $sql .= "h.rating >= :minRating AND ";
        $sql .= "h.rating <= :maxRating AND ";
        $sql .= "hr.price >= :minPrice AND ";
        $sql .= "hr.price <= :maxPrice AND ";
        $sql .= "hr.type = :roomType ";
        $sql .= "AND ( ";
        $sql .= "SELECT COUNT(b.id) ";
        $sql .= "FROM Booking b ";
        $sql .= "WHERE b.idHotelRoom = hr.id AND ";
        $sql .= "b.arrival >= :arrival ";
        $sql .= "AND b.departure <= :departure ";
        $sql .= ") == 0 ";
        $sql .= "GROUP BY hr.idHotel";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue("townName", $town);
        $stmt->bindValue("minRating", $minRating);
        $stmt->bindValue("maxRating", $maxRating);
        $stmt->bindValue("minPrice", $minRoomPrice);
        $stmt->bindValue("maxPrice", $maxRoomPrice);
        $stmt->bindValue("roomType", $roomType);
        $stmt->bindValue("arrival", $arrival);
        $stmt->bindValue("departure", $departure);
        $stmt->execute();

        return $stmt->fetchAll();
    }

}