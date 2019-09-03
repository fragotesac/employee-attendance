<?php

namespace Business\View\Helper;
use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;

class Acl extends AbstractHelper
{
    public function __invoke()
    {
       $acl = new Container('acl');
       return $acl->permission;
    }
}