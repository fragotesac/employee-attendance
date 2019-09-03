<?php

namespace Business\View\Helper;

use Zend\View\Helper\AbstractHelper;

class PKs extends AbstractHelper
{
    public function __invoke($pks, $data, $str = false, $onlyValues = false)
    {
       $urlParams = [];

       if ($str && $onlyValues) {
          $params = [];
          foreach ($pks as $key) {
             $params[] .= $data->{$key};
          }

          $urlParams = implode('-', $params);
       } else {
          foreach ($pks as $key) {
             $urlParams[$key] = $data->{$key};
          }
       }



       return $urlParams;
    }
}