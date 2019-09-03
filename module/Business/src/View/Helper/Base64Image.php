<?php

namespace Business\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Base64Image extends AbstractHelper
{
    public function __invoke($filePath = '')
    {
        if (is_file($filePath)) {
           $type = pathinfo($filePath, PATHINFO_EXTENSION);
           $data = file_get_contents($filePath);
           $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

           return $base64;
        }
    }
}