<?php

namespace App\Http\Responses\Api\Messages\News;

class GameNewsTranslation
{
    public function __construct(
        public string $language,
        public string $title,
        public string $body,
    )
    {
    }
}