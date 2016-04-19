<?php

namespace Letoh\Model;

class Booking {
	private $id;
	private $hotelRoom;
	private $customer;
	private $arrivalDate;
	private $departureDate;

	public function __construct($id, $hotelRoom, $customer, $arrivalDate, $departureDate) {
		$this->id = $id;
		$this->hotelRoom = $hotelRoom;
		$this->customer = $customer;
		$this->arrivalDate = $arrivalDate;
		$this->departureDate = $departureDate;
	}

	public function getId() {
		return $this->id;
	}

	public function getHotelRoom() {
		return $this->hotelRoom;
	}

	public function getCustomer() {
		return $this->customer;
	}

	public function getArrivalDate() {
		return $this->arrivalDate;
	}

	public function getDepartureDate() {
		return $this->departureDate;
	}
}

?>