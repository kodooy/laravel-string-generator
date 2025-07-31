<?php

namespace Kodooy\StringGenerator\Exceptions;

class InsufficientUniqueStringsException extends StringGeneratorException
{
    public function __construct(int $requested, int $generated, ?string $message = null)
    {
        $message = $message ?: "Could only generate {$generated} unique strings out of {$requested} requested.";

        parent::__construct($message);
    }
}
