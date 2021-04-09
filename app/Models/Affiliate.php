<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * The affiliate data source is currently a file, which may possibly
 * be subject to change over time, so a decision was made to put this
 * code in a model
 */
class Affiliate extends Model
{
    /**
     * @var integer DEGREES
     */
    const DEGREES = 60;

    /**
     * @var float KMCONVERSION
     */
    const KMCONVERSION = 1.609344;

    /**
     * @var float STATUTEMILE
     */
    const STATUTEMILE = 1.1515;

    /**
     * @var float $dubLat
     */
    private $dubLat;
        
    /**
     * @var float $dubLng
     */
    private $dubLng;
        
    /**
     * @var float $maxRangeDistance;
     */
    private $maxRangeDistance;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct() {
        /**
         * It gets Dublin office's latitude/longitude from config file
         * formatted to Float, also maximum range distance
         */
        $this->dubLat = floatval(config('app.dublin_office_latitude'));
        $this->dubLng = floatval(config('app.dublin_office_longitude'));
        $this->maxRangeDistance = floatval(config('app.max_range_distance'));
    }

    /**
     * Calculates the distance from a point to Dublin office
     *
     * @param  float $lat
     * @param  float $lng
     * @return float
     */
    private function calculateDistance($lat, $lng)
    {
        $theta = $this->dubLng - $lng;
        $x = deg2rad($this->dubLat);
        $y = deg2rad($lat);
        $dist  = rad2deg(acos(sin($x) * sin($y) +  cos($x) * cos($y) * cos(deg2rad($theta))));
        $miles = $dist * self::DEGREES * self::STATUTEMILE;

        return ($miles * self::KMCONVERSION);
    }
    
    /**
     * It gets affiliates from a specific file
     *
     * @return array
     */
    public function getAffiliates()
    {
        $affiliatesFile = Storage::path('public/affiliates.txt');

        try {
            $handle = fopen($affiliatesFile, 'r');
        } catch (\Throwable $e) {
            return ['status' => 'failed', 'data' => $e->getMessage()];
        }

        $structuredData = [];
        while (!feof($handle)) {
            $line = fgets($handle);

            $decodedLine = json_decode($line);

            $distance = $this->calculateDistance(floatval($decodedLine->latitude), floatval($decodedLine->longitude));

            if ($distance >= $this->maxRangeDistance) {
                continue;
            }

            $structuredData[$decodedLine->affiliate_id] = [
                'affiliate_id' => intval($decodedLine->affiliate_id),
                'distance'     => $distance,
                'latitude'     => floatval($decodedLine->latitude),
                'longitude'    => floatval($decodedLine->longitude),
                'name'         => $decodedLine->name
            ];
        }
        
        fclose($handle);

        return ['status' => 'success', 'data' => $structuredData];
    }
}
