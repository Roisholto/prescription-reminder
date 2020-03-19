<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\MedUsage ;
use App\Model\Meds ;
use App\Utils\CreateResponse ;
use App\Utils\JsonSchemaError ;

use Opis\JsonSchema\{
    Validator, ValidationResult, ValidationError, Schema
};

class MedUsageController extends Controller
{
  function __construct(){
    $this->middleware('auth:airlock') ;
  }

    //
  function store(Request $request){
    // ensure that the user

    $uid = Auth::id() ;

    $inputs = $request->input() ;
    $schema = Schema::fromJsonString(file_get_contents('../app/JsonSchema/med_usage.json'));
    $validator = new Validator() ;

    $o_inputs =  json_encode($inputs) ;
    $o_inputs =  json_decode($o_inputs) ;

    $result = $validator->schemaValidation( $o_inputs, $schema);

    if(!$result->isValid())
      return CreateResponse::create('01', 'Some error occured', JsonSchemaError::get($result)) ;

    $med_id = $request->input('med_id') ;
    $med_portion = $request->input('portion') ;
    $med = DB::select("SELECT
                        start_at,
                        dosage,
                        dosage_interval,
                        dosage_count,
                        start_at
                      FROM
                        meds
                      WHERE
                        id = ?
                        AND
                        users_id = ?
                      LIMIT 1",
                    [$med_id, $uid]
                    ) ;

    if(count($med))
      {
      $med = (array) $med[0] ;
      // get the total number of entries ;
      $usages = MedUsage::where('meds_id', $med_id)->get()->toArray() ;

      // ensure this portion has not be applied before ;
      $portions_applied = array_column($usages, 'portion') ;
      if(in_array($med_portion, $portions_applied))
        {
        return CreateResponse::create('01', 'Portion has been applied already') ;
        }

      $usages_count = count($usages) ;
      $max_count = floatval($med['dosage_count']) / floatval($med['dosage']) ;

      // ensure that the portion is within the sets if valids;
      // make it 0-index
      $valid_portion_sets = range(0,$max_count-1) ;
      if(!in_array($med_portion, $valid_portion_sets))
        {
        return CreateResponse::create('02', 'Portion not in the set of valid portions') ;
        }

      if($usages_count<$max_count)
        {
        // ensure it is the valid time for usage , i.e ;
        // current time is a time > the portion time i.e u cant use a portion for tomorrow today.
        // and
        $StartAt = new \DateTimeImmutable($med['start_at']) ;
        $itx = $med['dosage_interval'] * $med_portion ;
        $Interval = new \DateInterval('PT'.$itx.'S') ;
        $MinPortionTime = $StartAt->add($Interval) ;
        // allow a marginal error of 30 minutes ;
        $Now = new \DateTime() ;
        // return ;
        if($MinPortionTime->getTimestamp() < $Now->getTimestamp())
          {
          // insert the item ;
          $MedUsage = new MedUsage ;
          $MedUsage->meds_id = $med_id ;
          $MedUsage->portion = $med_portion ;
          $MedUsage->save() ;

          $MedUsage->refresh() ;

          return CreateResponse::create('1', 'Portion applied', ['entry'=>$MedUsage->toArray()]) ;
          }
        else
          {
          return CreateResponse::create('02', 'Min portion time not met, seems it is not high time you take this') ;
          }
        }
      else
        {
        return CreateResponse::create('01', 'max dosage reached') ;
        }
      // now ensure the total number of entries is not more than is needed ;
      print_r ($usages) ;
      }
    return $med ;
  }
}
