<?php

namespace App\Enums;

enum BannerTypeEnum: string
{
    case success = 'success';
    case danger = 'error';
    case warning = 'warn';
    case info = 'info';
}
