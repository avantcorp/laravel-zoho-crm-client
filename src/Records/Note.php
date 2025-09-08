<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Records;

class Note extends Record
{
    public function __construct(?string $title = null, string $content)
    {
        parent::__construct(array_filter([
            'Note_Title'   => $title,
            'Note_Content' => $content,
        ]));
    }

    public static function make(?string $title = null, string $content): static
    {
        return parent::make($content, $title);
    }
}
