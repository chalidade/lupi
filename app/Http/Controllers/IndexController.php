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
           $connect->leftJoin(strtoupper($list["table"]), strtoupper($list["field1"]), '=', strtoupper($list["field2"]));
         }
       }

       if (isset($input["join"])) {
         foreach ($input["join"] as $list) {
           $connect->join(strtoupper($list["table"]), strtoupper($list["field1"]), '=', strtoupper($list["field2"]));
         }
       }

       if (isset($input["whereraw"])) {
           $connect->whereRaw($input["whereraw"]);
       }

       if(isset($input["where"][0])) {
         $connect->where($input["where"]);
       }

       if(isset($input["whereIn"][0])) {
         $in        = $input["whereIn"];
         $connect->whereIn(strtoupper($in[0]), $in[1]);
       }

       if(isset($input["whereNotIn"][0])) {
         $in        = $input["whereNotIn"];
         $connect->whereNotIn(strtoupper($in[0]), $in[1]);
       }

       if (!empty($input["whereBetween"])) {
         $connect->whereBetween($input["whereBetween"][0],[$input["whereBetween"][1],$input["whereBetween"][2]]);
       }

       if(isset($input["groupby"])) {
         $connect->groupBy(strtoupper($input["groupby"]));
       }

       if (isset($input["selectraw"])) {
         $connect->select(DB::raw($input["selectraw"]));
       }

       if (isset($input["selected"])) {
         $connect->select($input["selected"]);
       }

       if(isset($input["orderBy"][0])) {
         $in        = $input["orderBy"];
         $connect->orderby(strtoupper($in[0]), $in[1]);
       }

       if (isset($input['start']) || $input["start"] == '0') {
         if (!empty($input['limit'])) {
           $connect->skip($input['start'])->take($input['limit']);
         }
       }

       $result = $connect->get();
       $count  = count($result);

       return ["result"=>$result, "count" => $count];
     }

     function testing($input) {
       return $input;
     }
}
