<?php

class GoogleLocation
{
    const STATUS_OK = 1;
    
    const STATUS_EMPTY_RESULT = 2;
    
    const STATUS_REQUEST_ERROR = 3;
    
    const STATUS_GOOGLE_RESPONSE_ERROR = 4;
    
    const STATUS_UNKNOWN_ERROR = 5;
    
    const PROXIMITY_NONE = 0;
    
    const PROXIMITY_COUNTRY = 10;
    
    const PROXIMITY_CITY = 20;
    
    const PROXIMITY_STREET = 30;
    
    const PROXIMITY_HOUSE = 40;
    
    public $country;
    
    public $city;
    
    public $street;
    
    public $house;
    
    public $zip;
    
    public $longitude;
    
    public $latitude;
    
    public $status;

    public $proximity = self::PROXIMITY_NONE;    
}