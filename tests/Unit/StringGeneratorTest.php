<?php

use Kodooy\StringGenerator\StringGenerator;
use Kodooy\StringGenerator\Exceptions\InsufficientUniqueStringsException;
use Kodooy\StringGenerator\Exceptions\InvalidCharsetException;
use Kodooy\StringGenerator\Events\StringGenerated;
use Kodooy\StringGenerator\Events\StringCollectionGenerated;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->generator = new StringGenerator();
});

describe('StringGenerator', function () {

    it('can generate a single string with default length', function () {
        $string = $this->generator->single();

        expect($string)->toBeString()
            ->and(strlen($string))->toBe(8); // default length from config
    });

    it('can generate a single string with custom length', function () {
        $length = 12;
        $string = $this->generator->single($length);

        expect($string)->toBeString()
            ->and(strlen($string))->toBe($length);
    });

    it('can generate string with excluded characters', function () {
        $exclude = 'abc123';
        $string = $this->generator->single(100, $exclude);

        foreach (str_split($exclude) as $char) {
            expect($string)->not->toContain($char);
        }
    });

    it('can generate a collection of unique strings', function () {
        $count = 5;
        $length = 6;
        $collection = $this->generator->collection($count, $length);

        expect($collection)->toHaveCount($count);
        expect($collection->unique())->toHaveCount($count); // All strings should be unique

        $collection->each(function ($string) use ($length) {
            expect($string)->toBeString()
                ->and(strlen($string))->toBe($length);
        });
    });

    it('throws exception when requesting too many unique strings', function () {
        // Try to generate more unique strings than possible with a very limited charset
        $this->generator->charset('ab'); // Only 2 characters

        expect(fn() => $this->generator->collection(100, 2)) // 2^2 = 4 max unique strings of length 2
            ->toThrow(InsufficientUniqueStringsException::class);
    });

    it('can use predefined charsets', function () {
        $string = $this->generator->numeric()->single(10);

        expect($string)->toMatch('/^[0-9]+$/');
    });

    it('can use custom charset', function () {
        $customCharset = 'xyz';
        $string = $this->generator->charset($customCharset)->single(10);

        expect($string)->toMatch('/^[xyz]+$/');
    });

    it('throws exception for empty charset', function () {
        expect(fn() => $this->generator->charset(''))
            ->toThrow(InvalidCharsetException::class);
    });

    it('throws exception when no characters available after exclusions', function () {
        expect(fn() => $this->generator->charset('abc')->single(5, 'abc'))
            ->toThrow(InvalidCharsetException::class);
    });

    it('can generate alphanumeric strings', function () {
        $string = $this->generator->alphanumeric()->single(20);

        expect($string)->toMatch('/^[a-zA-Z0-9]+$/');
    });

    it('can generate alpha strings', function () {
        $string = $this->generator->alpha()->single(20);

        expect($string)->toMatch('/^[a-zA-Z]+$/');
    });

    it('can generate lowercase strings', function () {
        $string = $this->generator->lowercase()->single(20);

        expect($string)->toMatch('/^[a-z]+$/');
    });

    it('can generate uppercase strings', function () {
        $string = $this->generator->uppercase()->single(20);

        expect($string)->toMatch('/^[A-Z]+$/');
    });

    it('validates length parameter', function () {
        expect(fn() => $this->generator->single(0))
            ->toThrow(InvalidArgumentException::class);

        expect(fn() => $this->generator->single(-1))
            ->toThrow(InvalidArgumentException::class);
    });

    it('validates count parameter', function () {
        expect(fn() => $this->generator->collection(0))
            ->toThrow(InvalidArgumentException::class);

        expect(fn() => $this->generator->collection(-1))
            ->toThrow(InvalidArgumentException::class);
    });

    it('can use generate method for single string', function () {
        $string = $this->generator->generate(10);

        expect($string)->toBeString()
            ->and(strlen($string))->toBe(10);
    });

    it('can use generate method for collection', function () {
        $collection = $this->generator->generate(8, 3);

        expect($collection)->toBeInstanceOf(\Illuminate\Support\Collection::class)
            ->and($collection)->toHaveCount(3);
    });

    it('fires events when generating strings', function () {
        Event::fake();

        $this->generator->single(5);

        Event::assertDispatched(StringGenerated::class, function ($event) {
            return $event->length === 5;
        });
    });

    it('fires events when generating collections', function () {
        Event::fake();

        $this->generator->collection(3, 6);

        Event::assertDispatched(StringCollectionGenerated::class, function ($event) {
            return $event->length === 6 && $event->count === 3;
        });
    });

    it('can chain methods fluently', function () {
        $string = $this->generator
            ->alphanumeric()
            ->single(15);

        expect($string)->toBeString()
            ->and(strlen($string))->toBe(15)
            ->and($string)->toMatch('/^[a-zA-Z0-9]+$/');
    });

    it('can use using method with predefined charsets', function () {
        $string = $this->generator->using('numeric')->single(8);

        expect($string)->toMatch('/^[0-9]+$/');
    });

    it('handles predefined charset names in charset method', function () {
        $string = $this->generator->charset('alpha')->single(10);

        expect($string)->toMatch('/^[a-zA-Z]+$/');
    });
});
