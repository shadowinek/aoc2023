<?php

namespace Shadowinek\Aoc2023;

class AoC
{
    private function readInput(int $puzzle, bool $real_input, bool $second_input): array
    {
        return file(
            sprintf(
                __DIR__ . '/../data/input_%s%s%s',
                $this->getNumberString($puzzle),
                $real_input ? '' : '_test',
                $second_input ? '_02' : ''
            ),FILE_IGNORE_NEW_LINES);
    }

    private function getNumberString(string $number): string
    {
        return sprintf('%02d', $number);
    }

    public function execute(int $puzzle, int $part, bool $real_input, bool $second_input = false): void
    {
        $data = $this->readInput($puzzle, $real_input, $second_input);
        $expected = include(__DIR__ . '/../data/expected.php');

        $class = 'Shadowinek\\Aoc2023\\Puzzle' . $this->getNumberString($puzzle);
        $method = 'runPart' . $this->getNumberString($part);

        echo 'Puzzle:   ' . $this->getNumberString($puzzle) . PHP_EOL;
        echo 'Part:     ' . $this->getNumberString($part) . PHP_EOL;
        echo 'Dataset:  ' . ($real_input ? 'real' : 'test') . PHP_EOL;
        echo '===============' . PHP_EOL;
        echo 'Output:   ' . (class_exists($class) ? (new $class($data))->$method() : 'This puzzle is not defined yet!') . PHP_EOL;
        echo 'Expected: ' . ($expected[$puzzle][$part][$real_input] ?? '-') . PHP_EOL;
    }
}
