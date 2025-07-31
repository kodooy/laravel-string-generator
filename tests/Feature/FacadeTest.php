<?php

use Kodooy\StringGenerator\Facades\StringGenerator;

describe('StringGenerator Facade', function () {

    it('can generate strings through facade', function () {
        $string = StringGenerator::single(10);

        expect($string)->toBeString()
            ->and(strlen($string))->toBe(10);
    });

    it('can chain methods through facade', function () {
        $string = StringGenerator::alphanumeric()->single(8);

        expect($string)->toBeString()
            ->and(strlen($string))->toBe(8)
            ->and($string)->toMatch('/^[a-zA-Z0-9]+$/');
    });

    it('can generate collections through facade', function () {
        $collection = StringGenerator::collection(3, 6);

        expect($collection)->toHaveCount(3);

        $collection->each(function ($string) {
            expect($string)->toBeString()
                ->and(strlen($string))->toBe(6);
        });
    });

    it('can use generate method through facade', function () {
        $singleString = StringGenerator::generate(5);
        expect($singleString)->toBeString()
            ->and(strlen($singleString))->toBe(5);

        $collection = StringGenerator::generate(4, 3);
        expect($collection)->toHaveCount(3);
    });
});
