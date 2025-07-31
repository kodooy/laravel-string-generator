<?php

namespace Kodooy\StringGenerator\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StringGenerated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $string,
        public int $length,
        public ?string $charset = null,
        public ?string $exclude = null
    ) {
        //
    }
}
