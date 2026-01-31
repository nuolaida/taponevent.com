<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 7/31/18
 * Time: 6:19 PM
 */

namespace BestPub\Pub\Service;


use BestPub\Geo\Service\Geocoder;
use BestPub\Pub\Model\Pub;

class PubLocation
{
    /**
     * Update location using geocoding
     *
     * @param $pubId
     * @return bool
     */
    public static function updateLocation($pubId)
    {
        $pub = Pub::fetch($pubId);
        $response = Geocoder::geocode($pub['address'], $pub['city'], $pub['country']);

        if ($response === null) {
            return false;
        }

        $geometry = $response->getGeometry();
        if ($geometry === null) {
            return false;
        }

        $coordinates = $geometry->getLocation();
        if ($coordinates !== null) {
            return Pub::update(
                $pubId,
                [
                    'coordinates_lat' => $coordinates->getLatitude(),
                    'coordinates_lng' => $coordinates->getLongitude()
                ]
            );
        }
    }
}