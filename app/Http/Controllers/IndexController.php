<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
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
       $input  = $this->request->all();
       $action = $input["action"];
       return $this->$action($input);
     }

     function list($input) {
       $connect = DB::connection($input["db"])->table($input["table"]);

       if (!empty($input["leftJoin"])) {
         foreach ($input["leftJoin"] as $list) {
           $connect->leftJoin($list["table"], $list["field1"], '=', $list["field2"]);
         }
       }

       if (isset($input["innerJoin"])) {
         foreach ($input["innerJoin"] as $list) {
           $connect->join($list["table"], $list["field1"], '=', $list["field2"]);
         }
       }

       if (isset($input["whereraw"])) {
           $connect->whereRaw($input["whereraw"]);
       }

       if(isset($input["where"][0])) {
         $connect->where($input["where"]);
       }

       if(isset($input["whereOr"][0])) {
         $connect->orWhere($input["where"]);
       }

       if(isset($input["whereIn"][0])) {
         $in        = $input["whereIn"];
         $connect->whereIn($in[0], $in[1]);
       }

       if(isset($input["whereNotIn"][0])) {
         $in        = $input["whereNotIn"];
         $connect->whereNotIn($in[0], $in[1]);
       }

       if (!empty($input["whereBetween"])) {
         $connect->whereBetween($input["whereBetween"][0],[$input["whereBetween"][1],$input["whereBetween"][2]]);
       }

       if(isset($input["groupby"])) {
         $connect->groupBy($input["groupby"]);
       }

       if(isset($input["raw"]["groupby"])) {
         $connect->groupBy(DB::raw($input["raw"]["groupby"]));
       }

       if (isset($input["raw"]["selected"])) {
         $connect->select(DB::raw($input["raw"]["selected"]));
       }

       if (isset($input["selected"])) {
         $connect->select($input["selected"]);
       }

       if(isset($input["orderBy"][0])) {
         $in        = $input["orderBy"];
         $connect->orderby($in[0], $in[1]);
       }

       if (isset($input['start'])) {
         if (!empty($input['limit'])) {
           $connect->skip($input['start'])->take($input['limit']);
         }
       }

       $result = $connect->get();
       $count  = count($result);

       return ["count" => $count, "result"=>$result];
     }

     function viewHeaderDetail($input) {
         $data    = $input["data"];
         $count   = count($input["data"]);
         $pk      = $input["HEADER"]["PK"][0];
         $pkVal   = $input["HEADER"]["PK"][1];
         foreach ($data as $data) {
           $val     = $input[$data];
           $connect  = DB::connection($val["DB"])->table($val["TABLE"]);
             if ($data == "HEADER") {
                $header   = $connect->where(strtoupper($pk), "like", strtoupper($pkVal))->get();
                $header   = json_decode(json_encode($header), TRUE);
                $vwdata = ["HEADER" => $header];
             }

             else if($data == "FILE") {
               if (isset($input[$data]["BASE64"])) {
                 if ($input[$data]["BASE64"] == "N" || $input[$data]["BASE64"] == "n" ) {
                   $fil     = [];
                   $fk      = $val["FK"][0];
                   $fkhdr   = $header[0][$val["FK"][1]];
                   $detail  = json_decode(json_encode($connect->where(strtoupper($fk), "like", strtoupper($fkhdr))->get()), TRUE);
                   foreach ($detail as $list) {
                     $newDt = [];
                     foreach ($list as $key => $value) {
                       $newDt[$key] = $value;
                     }
                     $fil[] = $newDt;
                     $vwdata[$data] = $fil;
                     }
                     if (empty($detail)) {
                       $vwdata[$data] = [];
                       }
                 }
               } else {
               $fil     = [];
               $fk      = $val["FK"][0];
               $fkhdr   = $header[0][$val["FK"][1]];
               $detail  = json_decode(json_encode($connect->where(strtoupper($fk), "like", strtoupper($fkhdr))->get()), TRUE);
               foreach ($detail as $list) {
                 $newDt = [];
                 foreach ($list as $key => $value) {
                   $newDt[$key] = $value;
                 }
                 $dataUrl = "http://10.88.48.33/api/public/".$detail[0]["doc_path"];
                 $url     = str_replace(" ", "%20", $dataUrl);
                 $file = file_get_contents($url);
                 $newDt["base64"]  =  base64_encode($file);
                 $fil[] = $newDt;
                 $vwdata[$data] = $fil;
                 }
                 if (empty($detail)) {
                   $vwdata[$data] = [];
                   }
                 }
               }

             else {
               $fk      = $val["FK"][0];
               $fkhdr   = $header[0][$val["FK"][1]];
               if(isset($val["WHERE"][0])) {
                 if (isset($val["JOIN"])) {
                   foreach ($val["JOIN"] as $list) {
                     $connect->join(strtoupper($list["table"]), strtoupper($list["field1"]), '=', strtoupper($list["field2"]));
                   }
                 }
                 if (isset($val["LEFTJOIN"])) {
                   foreach ($val["LEFTJOIN"] as $list) {
                     $connect->leftJoin(strtoupper($list["table"]), strtoupper($list["field1"]), '=', strtoupper($list["field2"]));
                   }
                 }
                 if (isset($val["JOINRAW"])) {
                   foreach ($val["JOINRAW"] as $list) {
                     $connect->join(strtoupper($list["table"]), DB::raw($list['field']));
                   }
                 }
                 $detail  = $connect->where(strtoupper($fk), "like", strtoupper($fkhdr))->where($val["WHERE"])->get();
               } else {
                 if (isset($val["JOIN"])) {
                   foreach ($val["JOIN"] as $list) {
                     $connect->join(strtoupper($list["table"]), strtoupper($list["field1"]), '=', strtoupper($list["field2"]));
                   }
                 }
                 if (isset($val["LEFTJOIN"])) {
                   foreach ($val["LEFTJOIN"] as $list) {
                     $connect->leftJoin(strtoupper($list["table"]), strtoupper($list["field1"]), '=', strtoupper($list["field2"]));
                   }
                 }
                 if (isset($val["JOINRAW"])) {
                   foreach ($val["JOINRAW"] as $list) {
                     $connect->join(strtoupper($list["table"]), DB::raw($list['field']));
                   }
                 }
                   $detail  = $connect->where(strtoupper($fk), "like", strtoupper($fkhdr))->get();
                 }

                 $vwdata[$data] = $detail;
               }
             }


         if (!empty($input["changeKey"])) {
           $result  = $vwdata;
           $data    = json_encode($result);
           $change  = str_replace($input["changeKey"][0], $input["changeKey"][1], $data);
           $vwdata  = json_decode($change);
         }

         if (isset($input["spesial"])) {
           if ($input["spesial"] == "TM_LUMPSUM") {
             $id       = $input["HEADER"]["PK"][1];
             $detail   = [];
             $cust     = [];
             $fil      = [];
             $data_a   = DB::connection("omcargo")->table('TS_LUMPSUM_AREA')->where("LUMPSUM_ID", "=", $id)->get();
             foreach ($data_a as $list) {
               $newDt = [];
               foreach ($list as $key => $value) {
                 $newDt[$key] = $value;
               }

               $data_c = DB::connection("omcargo")->table('TM_REFF')->where([["REFF_TR_ID", "=", "11"],["REFF_ID", "=", $list->lumpsum_stacking_type]])->get();
               foreach ($data_c as $listc) {
                 $newDt = [];
                 foreach ($listc as $key => $value) {
                   $newDt[$key] = $value;
                 }
               }

             if ($list->lumpsum_stacking_type == "2") {
               $data_b = DB::connection("mdm")->table('TM_STORAGE')->where("storage_code", $list->lumpsum_area_code)->get();
             } else {
               $data_b = DB::connection("mdm")->table('TM_YARD')->where("yard_code", $list->lumpsum_area_code)->get();
             }
             foreach ($data_b as $listS) {
               foreach ($listS as $key => $value) {
                 $newDt[$key] = $value;
               }
             }
             $detail[] = $newDt;
            }

            $data_d   = DB::connection("omcargo")->table('TS_LUMPSUM_CUST')->where("LUMPSUM_ID", "=", $id)->get();
            foreach ($data_d as $listD) {
              $custo = [];
              foreach ($listD as $key => $value) {
                $custo[$key] = $value;
              }
                $cust[] = $custo;
            }

            $vwdata["DETAIL"] = $detail;
            $vwdata["CUSTOMER"] = $cust;
            $no       = $vwdata["HEADER"][0]["lumpsum_no"];
            $data_e   = DB::connection("omcargo")->table('TX_DOCUMENT')->where("REQ_NO", "=", $id)->get();
            foreach ($data_e as $list) {
              $newDt = [];
              foreach ($list as $key => $value) {
                $newDt[$key] = $value;
              }
              $dataUrl = "http://10.88.48.33/api/public/".$list->doc_path;
              $url     = str_replace(" ", "%20", $dataUrl);
              $file = file_get_contents($url);
              $newDt["base64"]  =  base64_encode($file);
              $fil[] = $newDt;
              $vwdata["FILE"] = $fil;
              }
              if (empty($data_e)) {
                $vwdata["FILE"] = [];
              }
           }
         }
         return $vwdata;
       }

     function cekLogin($input) {
       $data = DB::connection($input["db"])->table($input["table"])->where([$input["value"]])->first();
       if (!empty($data)) {
         return ["message"=>"Login Success", "data"=>$data];
       } else {
         return ["message"=> "Check Your Username / Password"];
       }
     }

}
