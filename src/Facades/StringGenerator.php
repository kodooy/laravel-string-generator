<?php

namespace Kodooy\StringGenerator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Kodooy\StringGenerator\StringGenerator charset(string $charset)
 * @method static string|Illuminate\Support\Collection generate(int $length = null, ?int $count = null, ?string $exclude = null)
 * @method static string single(int $length = null, ?string $exclude = null)
 * @method static \Illuminate\Support\Collection collection(int $count, int $length = null, ?string $exclude = null)
 * @method static \Kodooy\StringGenerator\StringGenerator using(string $charsetName)
 * @method static \Kodooy\StringGenerator\StringGenerator alphanumeric()
 * @method static \Kodooy\StringGenerator\StringGenerator alpha()
 * @method static \Kodooy\StringGenerator\StringGenerator numeric()
 * @method static \Kodooy\StringGenerator\StringGenerator lowercase()
 * @method static \Kodooy\StringGenerator\StringGenerator uppercase()
 *
 * @see \Kodooy\StringGenerator\StringGenerator
 */
class StringGenerator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'string-generator';
    }
}
