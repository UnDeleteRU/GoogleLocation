<?php

namespace Undelete;

class GoogleLocationResolver
{
    
    private $language;
    
    private $browser;
    
    public function __construct($language = 'en')
    {
        $this->language = $language;
        $this->browser = new \Buzz\Browser(new \Buzz\Client\Curl());
    }
    
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function queryAddress($country, $city, $address)
    {
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query(array(
            'language' => $this->language,
            'sensor' => 'false',
            'address' => $country . ', ' . $city . ', ' . $address,
        ));
        
        $response = $this->browser->get($url);
        
        $data = new GoogleLocation;
        
        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getContent(), true);
            
            if ($result['status'] == 'OK') {
                if ($result['results']) {
                    $data->status = GoogleLocation::STATUS_OK;

                    $item = $result['results'][0];
                    
                    $data->latitude = $item['geometry']['location']['lat'];
                    $data->longitude = $item['geometry']['location']['lng'];
                    
                    foreach ($item['address_components'] as $component) {
                        $types = $component['types'];
                        
                        if (in_array('street_number', $types)) {
                            $data->house = $component['long_name'];
                        } elseif (in_array('route', $types)) {
                            $data->street = $component['long_name'];
                        } elseif (in_array('locality', $types)) {
                            $data->city = $component['long_name'];
                        } elseif (in_array('country', $types)) {
                            $data->country = $component['long_name'];
                        } elseif (in_array('postal_code', $types)) {
                            $data->zip = $component['long_name'];
                        }
                    }
                } else {
                    $data->status = GoogleLocation::STATUS_EMPTY_RESULT;
                }
            } elseif ($result['status'] == 'ZERO_RESULTS') {
                $data->status = GoogleLocation::STATUS_EMPTY_RESULT;
            } elseif (in_array ($result['status'], array('OVER_QUERY_LIMIT', 'REQUEST_DENIED', 'INVALID_REQUEST'))) {
                $data->status = GoogleLocation::STATUS_REQUEST_ERROR;
            } else {
                $data->status = GoogleLocation::STATUS_UNKNOWN_ERROR;
            }
        } else {
            $data->status = GoogleLocation::STATUS_GOOGLE_RESPONSE_ERROR;
        }
        
        if ($data->status == GoogleLocation::STATUS_OK) {
            if ($data->house) {
                $data->proximity = GoogleLocation::PROXIMITY_HOUSE;
            } elseif ($data->street) {
                $data->proximity = GoogleLocation::PROXIMITY_STREET;
            } elseif ($data->city) {
                $data->proximity = GoogleLocation::PROXIMITY_CITY;
            } elseif ($data->country) {
                $data->proximity = GoogleLocation::PROXIMITY_COUNTRY;
            }
        }
        
        return $data;
    }
}