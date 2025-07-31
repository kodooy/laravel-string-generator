<?php

namespace Kodooy\StringGenerator;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Kodooy\StringGenerator\Events\StringGenerated;
use Kodooy\StringGenerator\Events\StringCollectionGenerated;
use Kodooy\StringGenerator\Exceptions\InsufficientUniqueStringsException;
use Kodooy\StringGenerator\Exceptions\InvalidCharsetException;

class StringGenerator
{
    protected string $charset;
    protected int $defaultLength;
    protected int $maxAttemptsMultiplier;
    protected bool $cacheEnabled;
    protected int $cacheTtl;
    protected string $cachePrefix;
    protected bool $eventsEnabled;

    public function __construct()
    {
        $this->charset = config('string-generator.default_charset', 'abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789');
        $this->defaultLength = config('string-generator.default_length', 8);
        $this->maxAttemptsMultiplier = config('string-generator.max_attempts_multiplier', 5);
        $this->cacheEnabled = config('string-generator.cache.enabled', false);
        $this->cacheTtl = config('string-generator.cache.ttl', 3600);
        $this->cachePrefix = config('string-generator.cache.prefix', 'string_generator:');
        $this->eventsEnabled = config('string-generator.events.enabled', true);
    }

    /**
     * Set a custom charset for string generation.
     *
     * @param string $charset
     * @return static
     * @throws InvalidCharsetException
     */
    public function charset(string $charset): static
    {
        if (empty($charset)) {
            throw new InvalidCharsetException($charset);
        }

        // Check if it's a predefined charset
        $predefinedCharsets = config('string-generator.charsets', []);
        if (isset($predefinedCharsets[$charset])) {
            $this->charset = $predefinedCharsets[$charset];
        } else {
            $this->charset = $charset;
        }

        return $this;
    }

    /**
     * Generates a single string or a collection of unique strings with
     * given length. Excludes specific characters if provided.
     *
     * @param int|null $length
     * @param int|null $count
     * @param string|null $exclude
     * @return string|Collection
     * @throws InsufficientUniqueStringsException
     */
    public function generate(?int $length = null, ?int $count = null, ?string $exclude = null): string|Collection
    {
        $length = $length ?? $this->defaultLength;

        if ($length <= 0) {
            throw new \InvalidArgumentException('Length must be greater than 0');
        }

        if ($count !== null && $count <= 0) {
            throw new \InvalidArgumentException('Count must be greater than 0 or null');
        }

        if (is_null($count) || $count === 1) {
            return $this->generateSingle($length, $exclude);
        }

        return $this->generateUniqueCollection($length, $count, $exclude);
    }

    /**
     * Generate a single string.
     *
     * @param int|null $length
     * @param string|null $exclude
     * @return string
     */
    public function single(?int $length = null, ?string $exclude = null): string
    {
        $length = $length ?? $this->defaultLength;

        if ($length <= 0) {
            throw new \InvalidArgumentException('Length must be greater than 0');
        }

        return $this->generateSingle($length, $exclude);
    }

    /**
     * Generate a collection of unique strings.
     *
     * @param int $count
     * @param int|null $length
     * @param string|null $exclude
     * @return Collection
     * @throws InsufficientUniqueStringsException
     */
    public function collection(int $count, ?int $length = null, ?string $exclude = null): Collection
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException('Count must be greater than 0');
        }

        return $this->generateUniqueCollection($length ?? $this->defaultLength, $count, $exclude);
    }

    /**
     * Generates a collection of unique strings.
     *
     * @param int $length
     * @param int $count
     * @param string|null $exclude
     * @return Collection
     * @throws InsufficientUniqueStringsException
     */
    private function generateUniqueCollection(int $length, int $count, ?string $exclude): Collection
    {
        $cacheKey = null;
        if ($this->cacheEnabled) {
            $cacheKey = $this->cachePrefix . md5(serialize([$length, $count, $exclude, $this->charset]));
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return collect($cached);
            }
        }

        $strings = collect();
        $attempts = 0;
        $maxAttempts = $count * $this->maxAttemptsMultiplier;

        while ($strings->count() < $count && $attempts < $maxAttempts) {
            $newString = $this->generateSingle($length, $exclude, false); // Don't fire events for individual strings

            if (!$strings->contains($newString)) {
                $strings->push($newString);
            }

            $attempts++;
        }

        if ($strings->count() < $count) {
            throw new InsufficientUniqueStringsException($count, $strings->count());
        }

        if ($this->cacheEnabled && $cacheKey) {
            Cache::put($cacheKey, $strings->toArray(), $this->cacheTtl);
        }

        if ($this->eventsEnabled) {
            Event::dispatch(new StringCollectionGenerated($strings, $length, $count, $this->charset, $exclude));
        }

        return $strings;
    }

    /**
     * Generates a single string of the specified length.
     *
     * @param int $length
     * @param string|null $exclude
     * @param bool $fireEvent
     * @return string
     */
    private function generateSingle(int $length, ?string $exclude, bool $fireEvent = true): string
    {
        $cacheKey = null;
        if ($this->cacheEnabled) {
            $cacheKey = $this->cachePrefix . 'single:' . md5(serialize([$length, $exclude, $this->charset, microtime()]));
        }

        $availableChars = $this->charset;

        if ($exclude) {
            $availableChars = str_replace(str_split($exclude), '', $availableChars);
        }

        if (empty($availableChars)) {
            throw new InvalidCharsetException('No characters available after exclusions');
        }

        $charCount = strlen($availableChars);
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $availableChars[random_int(0, $charCount - 1)];
        }

        if ($this->cacheEnabled && $cacheKey) {
            Cache::put($cacheKey, $string, $this->cacheTtl);
        }

        if ($fireEvent && $this->eventsEnabled) {
            Event::dispatch(new StringGenerated($string, $length, $this->charset, $exclude));
        }

        return $string;
    }

    /**
     * Generate strings using a specific predefined charset.
     *
     * @param string $charsetName
     * @return static
     * @throws InvalidCharsetException
     */
    public function using(string $charsetName): static
    {
        return $this->charset($charsetName);
    }

    /**
     * Generate alphanumeric strings (safe characters only).
     *
     * @return static
     */
    public function alphanumeric(): static
    {
        return $this->charset('alphanumeric_safe');
    }

    /**
     * Generate alphabetic strings only.
     *
     * @return static
     */
    public function alpha(): static
    {
        return $this->charset('alpha_safe');
    }

    /**
     * Generate numeric strings only.
     *
     * @return static
     */
    public function numeric(): static
    {
        return $this->charset('numeric');
    }

    /**
     * Generate lowercase strings only.
     *
     * @return static
     */
    public function lowercase(): static
    {
        return $this->charset('lowercase');
    }

    /**
     * Generate uppercase strings only.
     *
     * @return static
     */
    public function uppercase(): static
    {
        return $this->charset('uppercase');
    }
}
