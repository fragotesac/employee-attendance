<?php

namespace Business\Util;

class Fecha
{
   public static function mesDsc($mesNro)
   {
      $mes = '';
      switch ($mesNro) {
         case '1':
         case '01':
            $mes = 'ENERO';
            break;
         case '2':
         case '02':
            $mes = 'FEBRERO';
            break;
         case '3':
         case '03':
            $mes = 'MARZO';
            break;
         case '4':
         case '04':
            $mes = 'ABRIL';
            break;
         case '5':
         case '05':
            $mes = 'MAYO';
            break;
         case '6':
         case '06':
            $mes = 'JUNIO';
            break;
         case '7':
         case '07':
            $mes = 'JULIO';
            break;
         case '8':
         case '08':
            $mes = 'AGOSTO';
            break;
         case '9':
         case '09':
            $mes = 'SETIEMBRE';
            break;
         case '10':
            $mes = 'OCTUBRE';
            break;
         case '11':
            $mes = 'NOVIEMBRE';
            break;
         case '12':
            $mes = 'DICIEMBRE';
            break;
      }

      return $mes;
   }
}