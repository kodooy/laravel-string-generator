<?php

namespace Kodooy\StringGenerator\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class StringCollectionGenerated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Collection $strings,
        public int $length,
        public int $count,
        public ?string $charset = null,
        public ?string $exclude = null
    ) {
        //
    }
}
