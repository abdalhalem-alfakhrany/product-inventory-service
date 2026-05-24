<?php

namespace App\Enum;

enum ProductStatus: string
{
    case Active = 'active';
    case InActive = 'inactive';
    case Discontinued = 'discontinued';
}
