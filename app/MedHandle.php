<?php
namespace App ;

use Opis\JsonSchema\{
    Validator, ValidationResult, ValidationError, Schema
};
use Illuminate\Support\Facades\DB;
use App\Utils\JsonSchemaError ;
use App\Utils\CreateResponse ;
use App\Model\Meds ;
use App\Model\MedGroup ;
use App\Model\MedUSage ;

class MedHandle {
  public static function create(int $uid, array $inputs){
    $schema = Schema::fromJsonString(file_get_contents(__DIR__.'/JsonSchema/add_med.json'));
    $validator = new Validator() ;

    $o_inputs =  json_encode($inputs) ;
    $o_inputs =  json_decode($o_inputs) ;

    $result = $validator->schemaValidation( $o_inputs, $schema);

    if($result->isValid())
      {
      DB::beginTransaction();
      $MedGroup = new MedGroup ;
      $MedGroup->user_id = $uid ;
      $MedGroup->label = $inputs['label'] ;
      $MedGroup->created_at = date('Y-m-d H:i:s') ;
      $MedGroup->notes = isset($inputs['notes']) ? $inputs['notes']: '' ;

      // echo ' uid ',$uid ;
      $meds = $inputs['meds'];
      foreach($meds as $i=>$med){
        $meds[$i]['users_id'] = strval($uid) ;
      }

      $MedGroup->save() ;
      // $MedGroup->id ;
      $meds = $MedGroup->meds()->createMany($meds) ;
      $rs = MedGroup::with(['meds'])->find($MedGroup->id)->toArray() ;
      DB::commit();
      return CreateResponse::create('1', 'created', $rs) ;
      }
    else
      {
      return CreateResponse::create('01', 'Some error occured', JsonSchemaError::get($result)) ;
      }
  }

  public static function getGroupWithMed(int $uid, int $gid=0){
    if($gid)
      $rs = MedGroup::with(['meds', 'meds.medUsage'])->where('user_id', $uid)->find($gid)->toArray() ;
    else
      $rs = MedGroup::with(['meds', 'meds.medusage'])->where('user_id', $uid)->get()->toArray() ;

    return CreateResponse::create('1', 'fetched', $rs) ;

  }

  public static function getUsage(int $uid, int $med_id){

  }
}
?>
