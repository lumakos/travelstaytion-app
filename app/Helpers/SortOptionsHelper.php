<?php

namespace App\Helpers;

class SortOptionsHelper
{
    public const LIKES = 'likes';
    public const HATES = 'hates';
    public const LATEST = 'latest';
    public const CREATED_AT = 'created_at';

    public const LABELS = [
        self::LIKES => 'Likes',
        self::HATES => 'Hates',
        self::LATEST => 'Dates',
    ];
}
