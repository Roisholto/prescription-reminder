<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MedGroup extends Model
{
    //

  protected $table='med_group' ;

  public $timestamps = false;

  function meds(){
    return $this->hasMany('App\Model\Meds') ;
  }
}
