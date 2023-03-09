<?php

namespace DnSoft\Media\Facades;

use Illuminate\Support\Facades\Facade;
use DnSoft\Media\ConversionRegistry;

class Conversion extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ConversionRegistry::class;
    }
}
