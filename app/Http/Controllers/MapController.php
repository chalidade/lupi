<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index() {
      $map = DB::connection('map')->table('location')->inRandomOrder()->first();
      $map = json_decode(json_encode($map), TRUE);

      $json = '
              {
                "geometry": {
                  "type": "Point",
                  "coordinates": [
                    '.$map["LOCATION_LONG"].',
                    '.$map["LOCATION_LAT"].'
                  ]
                },
                "type": "Feature",
                "properties": {}
              }';

      return $json;
    }
}
