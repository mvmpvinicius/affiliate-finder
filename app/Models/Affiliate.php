<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Affiliate extends Model
{
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
        $dist  = sin(deg2rad($this->dubLat)) * sin(deg2rad($lat)) +  cos(deg2rad($this->dubLat)) * cos(deg2rad($lat)) * cos(deg2rad($theta));
        $dist  = acos($dist);
        $dist  = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return ($miles * 1.609344);
    }
    
    /**
     * It gets affiliates from a specific file
     *
     * @return object
     */
    public function getAffiliates()
    {
        $affiliatesFile = Storage::path('public/affiliates.txt');

        try {
            $handle = fopen($affiliatesFile, 'r');
        } catch (\Throwable $e) {
            dd($e);
        }

        $structuredData = [];
        while (!feof($handle)) {
            $line = fgets($handle);

            $decodedLine = json_decode($line);

            $distance = $this->calculateDistance(floatval($decodedLine->latitude), floatval($decodedLine->longitude));

            if ($distance <= $this->maxRangeDistance) {
              $structuredData[$decodedLine->affiliate_id] = [
                  'affiliate_id' => intval($decodedLine->affiliate_id),
                  'distance'     => $distance,
                  'latitude'     => floatval($decodedLine->latitude),
                  'longitude'    => floatval($decodedLine->longitude),
                  'name'         => $decodedLine->name
              ];
            }
        }
        
        fclose($handle);

        return $structuredData;
    }
}
