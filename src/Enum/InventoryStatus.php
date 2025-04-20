<?php

namespace App\Enum;

enum InventoryStatus: string {
    case INSTOCK = 'INSTOCK';
    case LOWSTOCK = 'LOWSTOCK';
    case OUTOFSTOCK = 'OUTOFSTOCK';
}