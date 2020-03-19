<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Meds extends Model
{
  /*protected $fillable = [
    'med_group_id',
    'user_id',
    'name',
    'dosage',
    'interval',
    'dosage_count',
    'start_at'
    ] ;*/
    //
  protected $guarded = [] ;

  protected $table='meds' ;

  function setUserIdAttribute($x){
    return strval($x) ;
  }

  function getDosageAttribute($value){
      return floatval($value) ;
  }

  function getDosageCountAttribute($value){
      return floatval($value) ;
  }

  function getDosageIntervalAttribute($value){
      return floatval($value) ;
  }

  function users(){
    $this->belongsTo('App\Users') ;
  }

  function medGroup(){
    return $this->hasOne('App\Model\MedGroup') ;
  }

  function medUsage(){
    return $this->hasMany('App\Model\MedUsage') ;
  }
}
