<?php

/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 27/03/2016
 * Time: 13:06
 */

namespace Letoh\Model;

class Town {

    private $id;
    private $name;
    private $latitude;
    private $longitude;

    public static function create($data) {
        return new self($data['id'], $data['name'], $data['latitude'], $data['longitude']);
    }

    public function __construct($id, $name, $latitude, $longitude) {
        $this->id = $id;
        $this->name = $name;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getLatitude() {
        return $this->latitude;
    }
    
    public function getLongitude() {
        return $this->longitude;
    }

}