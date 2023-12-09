<?php

namespace Shadowinek\Aoc2023;

abstract class AbstractPuzzle
{
    public function __construct(protected readonly array $data)
    {
    }

    abstract public function runPart01(): int;
    abstract public function runPart02(): int;

    protected function parseNumbers(string $input): array
    {
        $numbers = [];

        preg_match_all('/(-?\d+)/', $input, $numbers);

        return $numbers[0];
    }
}
