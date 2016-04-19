<?php

namespace Letoh\Model;

class Hotel {
	private $id;
	private $idTown;
	private $name;
	private $address;
	private $rating;

	public static function create($data) {
		return new self($data['id'], $data['idTown'], $data['name'], $data['address'], $data['rating']);
	}

    public function __construct($id, $idTown, $name, $address, $rating) {
        $this->id = $id;
        $this->idTown = $idTown;
        $this->name = $name;
        $this->address = $address;
        $this->rating = $rating;
    }

    public function getId() {
		return $this->id;
	}

	public function getIdTown() {
		return $this->idTown;
	}

	public function getName() {
		return $this->name;
	}

	public function getAddress() {
		return $this->address;
	}

	public function getRating() {
		return $this->rating;
	}

}

?>