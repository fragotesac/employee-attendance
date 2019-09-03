<?php

namespace Business\Util;

class ArrayConversor
{
   static public function llaveValor($array, $keyName, $valueName, $emptyValue = false, $todosValue = false, $ucWords = false)
   {
      $newArray = [];

      if ($emptyValue) {
         $newArray[' '] = ' - Seleccione - ';
      }

      if ($todosValue) {
         $newArray['%'] = ' - TODOS - ';
      }

      foreach ($array as $key => $value) {
         if ($ucWords) {
            $newArray[$value[$keyName]] = ucwords(strtolower($value[$valueName]));
         } else {
            $newArray[$value[$keyName]] = $value[$valueName];
         }

      }

      return $newArray;
   }
}