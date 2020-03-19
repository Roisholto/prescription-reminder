<?php
namespace App\Utils ;

use Opis\JsonSchema\{
    Validator, ValidationResult, ValidationError, Schema
};

class JsonSchemaError {
  static function get(ValidationResult $result) : array
    {
      $error = $result->getFirstError();
      $resp =  [
        'keyword'=>$error->keyword(),
        'args'=>$error->keywordArgs()
        ] ;

      return $resp ;
    }
}
