<?php

namespace Dnsoft\Media\Facades;

use Illuminate\Support\Facades\Facade;
use Dnsoft\Media\ConversionRegistry;

class Conversion extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ConversionRegistry::class;
    }
}
