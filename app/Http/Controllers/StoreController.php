<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct(Request $request)
     {
         $this->request = $request;
     }

     public function api(Request $request) {
       $input  = $this->request->all(); // Get All Data From JSON
       $action = $input["action"];      // Get Function Name
       return $this->$action($input);   // Jump to function
     }

     function simpleSave($input) {
       // Initialization
       $primaryKey                = $input["primaryKey"];
       $connect                   = DB::connection($input["db"])->table($input["table"]);
       $data                      = [];

       // Get Last Sequece Data From DB
       $sequence                  = DB::connection($input["db"])->table(strtoupper($input["table"]))->orderBy($primaryKey, "DESC")->take(1)->select("$primaryKey as seq")->get();
       try {
         $seq                     = ($sequence[0]->seq)+1;
       } catch (\ErrorException $e) {
         $seq                     = 0;
       }

       // Put Data Sequence Into To Be Primary Key Value
       foreach ($input["value"] as  $value) {
         if (empty($value[$primaryKey])) {
               $newDt             = [];
               foreach ($value as $key => $value) {
                 if ($key == $primaryKey) {
                   $newDt[$key]   = $seq;
                   $seq++;
                 } else {
                   $newDt[$key]   = $value;
                 }
               }
                 $datahdr[]       = $newDt;
                 $data[]          = $newDt;
             }
         }

         // Data New For Insert Process Ready
         try {
           $datahdr                 =  json_decode(json_encode($datahdr),TRUE);
         } catch (\ErrorException $e) {
           $datahdr                 = 0;
         }


         //Start Loop Data if Data Not Exist then Insert and If data Exist Than Update
         $id                      = 0;
         foreach ($input["value"] as  $value) {
           if (empty($value[$primaryKey])) {
             $insert              = $connect->insert([$datahdr[$id]]);
             $result[]            = "Success Insert";
             $id++;
            } else {
             $update              = DB::connection($input["db"])->table($input["table"])->where($primaryKey,$value[$primaryKey])->update($value);
             $result[]            = "Success Update";
             $data[]              = $value;
            }
          }

          // Get Result
         return ["message" => $result, "data"=>$data];
       }

     function simpleDelete($input) {
       $connect = DB::connection($input["db"])->table($input["table"])->where($input["where"][0],$input["where"][1])->delete();
       echo "Delete Successfully";
     }

}
