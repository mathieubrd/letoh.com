<?php

namespace Letoh\Model;

class Customer {
	private $id;
	private $username;
	private $firstName;
	private $lastName;
	private $address;
	private $mail;

	public function __construct($id, $lastName, $firstName, $address, $mail, $idTown) {
		$this->id = $id;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->address = $address;
		$this->mail = $mail;
	}

	public function getId() {
		return $this->id;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function getAddress() {
		return $this->address;
	}

	public function getMail() {
		return $this->mail;
	}

}

?>