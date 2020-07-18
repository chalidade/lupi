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
       $sequence                  = DB::connection($input["db"])->table($input["table"])->orderBy($primaryKey, "DESC")->take(1)->select("$primaryKey as seq")->get();
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
           // If parameter Check Exist
           if (isset($input["checkFirst"])) {
             $check   = DB::connection($input["db"])->table($input["table"])->where($primaryKey, $value[$primaryKey])->first();
             if (empty($check)) {
               $insert              = $connect->insert([$datahdr[$id]]);
               $result[]            = "Success Insert";
               $id++;
             } else {
               $update              = DB::connection($input["db"])->table($input["table"])->where($primaryKey,$value[$primaryKey])->update($value);
               $result[]            = "Success Update";
               $data[]              = $value;
             }
           } else {
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
        }

          // Get Result
         return ["message" => $result, "data"=>$data];
      }

     function simpleDelete($input) {
       $connect = DB::connection($input["db"])->table($input["table"])->where($input["where"][0],$input["where"][1])->delete();
       echo "Delete Successfully";
     }

     function saveheaderdetail($input) {
         $data    = $input["data"];
         $count   = count($input["data"]);
         $cek     = $input["HEADER"]["PK"];
         $dbhdr   = $input["HEADER"]["DB"];
         $tblhdr  = $input["HEADER"]["TABLE"];

         foreach ($data as $data) {
           $val          = $input[$data];
           $connect      = DB::connection($val["DB"])->table($val["TABLE"]);
           if ($data == "HEADER") {
             $sequence   = DB::connection($dbhdr)->table($tblhdr)->orderBy($cek, "DESC")->first();
             if (empty($sequence)) {
               $seq = 1;
             } else {
               $seq      = ($sequence->$cek)+1;
             }

             $hdr        = json_decode(json_encode($val["VALUE"]), TRUE);

             if (!empty($hdr[0][$cek])) {
               foreach ($val["VALUE"] as $list) {
                 $newDt = [];
                 foreach ($list as $key => $value) {
                   if ($key == $cek) {
                     $newDt[$key] = $hdr[0][$cek];
                   } else {
                     $newDt[$key] = $value;
                   }
                 }
               }

                 $datahdr[] = $newDt;
                 $delete = DB::connection($dbhdr)->table($tblhdr)->where($cek,$hdr[0][$cek])->delete();
                 foreach ($datahdr as $value) {
                   $insert       = $connect->insert([$value]);
                 }
                 $header   = DB::connection($dbhdr)->table($tblhdr)->where($cek, $hdr[0][$cek])->first();
             } else {
               foreach ($val["VALUE"] as $list) {
                 $newDt = [];
                 foreach ($list as $key => $value) {
                   if ($key == $cek) {
                     $newDt[$key] = $seq;
                   } else {
                     $newDt[$key] = $value;
                   }
                 }
               }

                 $datahdr[] = $newDt;
                 foreach ($datahdr as $value) {
                   $insert       = $connect->insert([$value]);
                 }
                 $header   = DB::connection($dbhdr)->table($tblhdr)->where($cek, $seq)->first();
             }

             $header   = json_decode(json_encode($header), TRUE);
           } else {
             if (!empty($hdr[0][$cek])) {
               $connect->where($val["FK"][0], $header[$val["FK"][1]]);
               $connect->delete();
             }
             foreach ($val["VALUE"] as $value) {
               $addVal = [$val["FK"][0]=>$header[$val["FK"][1]]]+$value;
                   if(empty($value["id"])) {
                     $connect->insert([$addVal]);
                   }
               }
             }
           }
         return ["result"=>"Save or Update Success", "header"=>$header];
     }

     public static function delHeaderDetail($input) {
       $data    = $input["data"];
       $count   = count($input["data"]);
       $pk      = $input["HEADER"]["PK"][0];
       $pkVal   = $input["HEADER"]["PK"][1];
       foreach ($data as $data) {
         $val     = $input[$data];
         $connect  = DB::connection($val["DB"])->table($val["TABLE"]);
           if ($data == "HEADER") {
              $header   = $connect->where($pk, "like", $pkVal)->get();
              $header   = json_decode(json_encode($header), TRUE);
           }

           else if($data == "FILE") {
             $fil     = [];
             $fk      = $val["FK"][0];
             $fkhdr   = $header[0][$val["FK"][1]];
             $detail  = $connect->where($fk, "like", "%".$fkhdr."%")->first();
               if (file_exists($detail->doc_path)) {
                 $file    = unlink($detail->doc_path);
                 $result["file"] = "File delete success";
               } else {
                 $result["file"] = "Error Delete File / File Not Found";
               }
             }

           else {
             $fk      = $val["FK"][0];
             $fkhdr   = $header[0][$val["FK"][1]];
             $detail  = $connect->where($fk, "like",  "%".$fkhdr."%")->delete();
           }
       }
       $result["data"] = " Delete Success";
       $delHead = DB::connection($input["HEADER"]["DB"])->table($input["HEADER"]["TABLE"])->where($pk, "like", $pkVal)->delete();
       return $result;
     }

     public static function update($input){
       $connection = DB::connection($input["db"])->table($input["table"]);
       if (isset($input["where"])) {
         $connection->where($input["where"]);
       }
       if(isset($input["whereNotIn"][0])) {
       $in        = $input["whereNotIn"];
       $connection->whereNotIn($in[0], $in[1]);
       }

       if(!empty($input["whereIn"][0])) {
       $in        = $input["whereIn"];
       $connection->whereIn($in[0], $in[1]);
       }

       $connection->update($input["update"]);
       $data = $connection->get();
       return ["message"=>"Success", "result"=>$data];
     }

}
