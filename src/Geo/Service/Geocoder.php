<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 7/30/18
 * Time: 7:29 PM
 */

namespace BestPub\Geo\Service;



use BestPub\Utils\Services\Config;
use Http\Adapter\Guzzle6\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Ivory\GoogleMap\Service\Geocoder\GeocoderService;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderAddressRequest;
use Ivory\GoogleMap\Service\Serializer\SerializerBuilder;

class Geocoder
{
    /**
     * Geocode pub address
     *
     * @param $address
     * @param $city
     * @param $country
     *
     * @return \Ivory\GoogleMap\Service\Geocoder\Response\GeocoderResult|null
     */
    public static function geocode($address, $city, $country) {
        $geocoder = new GeocoderService(
            new Client(),
            new GuzzleMessageFactory(),
            SerializerBuilder::create()
        );
        $geocoder->setKey(Config::getConfigValue('maps_api_key'));

        $request = new GeocoderAddressRequest(implode(',', [$address, $city, $country]));

        $result = $geocoder->geocode($request);

        if ($result->getStatus() !== 'OK') {
            return null;
        }

        return $result->getResults()[0];
    }
}