<?php

namespace Letoh\Model;

class HotelRoom {
	
	private $id;
	private $idHotel;
	private $price;
	private $capacity;
	private $type;

	public static function create($data) {
		return new self($data['id'], $data['idHotel'], $data['price'], $data['capacity'], $data['type']);
	}

	public function __construct($id, $idHotel, $price, $capacity, $type) {
		$this->id = $id;
		$this->idHotel = $idHotel;
		$this->price = $price;
		$this->capacity = $capacity;
		$this->type = $type;
	}

	public function getId() {
		return $this->id;
	}

	public function getIdHotel() {
		return $this->idHotel;
	}

	public function getPrice() {
		return $this->price;
	}

	public function getCapacity() {
		return $this->capacity;
	}
	
	public function getType() {
		return $this->type;
	}

}

?>