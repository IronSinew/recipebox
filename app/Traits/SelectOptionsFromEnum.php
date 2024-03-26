<?php

namespace App\Traits;

use JetBrains\PhpStorm\ArrayShape;

trait SelectOptionsFromEnum
{
    #[ArrayShape(['name' => 'string', 'value' => 'string'])]
    public static function toSelectOptions(): array
    {
        return collect(self::cases())->transform(fn ($category) => [
            'name' => $category->label(),
            'value' => $category->value,
        ])->toArray();
    }
}
