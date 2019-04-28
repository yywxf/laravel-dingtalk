<?php

namespace Yywxf\Dingtalk\Facades;

use Illuminate\Support\Facades\Facade;

class Ding extends Facade
{
    protected static function getFacadeAccessor()
    {
        return '\Yywxf\Dingtalk\Dingtalk';
    }
}