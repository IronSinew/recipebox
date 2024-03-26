<?php

namespace App\Enums;

use App\Traits\DefaultLabelsFromEnum;
use App\Traits\SelectOptionsFromEnum;

enum UserRoleEnum: string
{
    use DefaultLabelsFromEnum;
    use SelectOptionsFromEnum;

    case MEMBER = '';
    case ADMIN = 'admin';
    case CONTRIBUTOR = 'contributor';
}
