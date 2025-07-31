<?php

namespace Kodooy\StringGenerator\Exceptions;

class InvalidCharsetException extends StringGeneratorException
{
    public function __construct(string $charset)
    {
        parent::__construct("Invalid charset '{$charset}'. Charset must be a non-empty string or a valid predefined charset name.");
    }
}
