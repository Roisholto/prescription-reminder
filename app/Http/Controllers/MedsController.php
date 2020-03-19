<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\Meds ;

use Opis\JsonSchema\{
    Validator, ValidationResult, ValidationError, Schema
};
use App\Utils\JsonSchemaError ;
use App\Utils\CreateResponse ;

class MedsController extends Controller
{
    function __construct(){
      $this->middleware('auth:airlock') ;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      //
      $inputs = $request->input() ;
      $id = Auth::id();
      $schema = Schema::fromJsonString(file_get_contents('../app/JsonSchema/meds.json'));
      $validator = new Validator() ;

      $o_inputs =  json_encode($inputs) ;
      $o_inputs =  json_decode($o_inputs) ;

      $result = $validator->schemaValidation( $o_inputs, $schema);
      if($result->isValid())
        {
        $ent = [] ;
        for($i= 0; $i < count($inputs) ; $i++)
          {
          $Meds = new Meds ;
          $inputs[$i]['users_id'] = $id ;
          $Meds->fill($inputs[$i]) ;
          $Meds->save() ;
          $ent[] = $Meds->id  ;
          }

        return CreateResponse::create('1', 'meds created', Meds::whereIn('id', $ent)->get()->toArray());
        }
      else
        {
        return CreateResponse::create('01', 'Some error occured', JsonSchemaError::get($result)) ;
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $inputs = $request->input() ;
      $uid = Auth::id();
      $schema = Schema::fromJsonString(file_get_contents('../app/JsonSchema/single_med.json'));
      $validator = new Validator() ;

      $o_inputs =  json_encode($inputs) ;
      $o_inputs =  json_decode($o_inputs) ;

      $result = $validator->schemaValidation( $o_inputs, $schema);
      if($result->isValid())
        {
        $ent = [] ;
        $Meds = Meds::where('users_id', $uid)->find($id) ;
        $Meds->fill($inputs) ;
        $Meds->save() ;

        return CreateResponse::create('1', 'meds created', $Meds->toArray());
        }
      else
        {
        return CreateResponse::create('01', 'Some error occured', JsonSchemaError::get($result)) ;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id=null)
    {
        //
      $inputs = $id ? ['id'=>[$id]] : $request->input() ;
      $schema = Schema::fromJsonString(file_get_contents('../app/JsonSchema/delete_meds.json'));
      $validator = new Validator() ;

      $o_inputs =  json_encode($inputs) ;
      $o_inputs =  json_decode($o_inputs) ;

      $result = $validator->schemaValidation( $o_inputs, $schema);
      if($result->isValid())
        {
        $delete = Meds::where('users_id', Auth::id())->whereIn('id', $inputs)->delete() ;
        $rs = ($delete) ? ['1', 'items deleted'] : ['01', 'error deleting items'] ;
        return CreateResponse::create(...$rs) ;
        }
      else
        {
        return CreateResponse::create('01', 'Some error occured', JsonSchemaError::get($result)) ;
        }
    }
}
