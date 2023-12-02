<?php

namespace Shadowinek\Aoc2023;

class Puzzle01 extends AbstractPuzzle
{
    private array $numbers = [];
    private array $map = [
        'one' => 1,
        'two' => 2,
        'three' => 3,
        'four' => 4,
        'five' => 5,
        'six' => 6,
        'seven' => 7,
        'eight' => 8,
        'nine' => 9,
    ];

    public function runPart01(): int
    {
        $this->loadData01();

        return array_sum($this->numbers);
    }

    public function runPart02(): int
    {
        $this->loadData02();

        return array_sum($this->numbers);
    }

    private function loadData01(): void
    {
        foreach ($this->data as $id => $data) {
            $chars = str_split($data);

            foreach ($chars as $char) {
                if ((int) $char) {
                    $this->numbers[$id] = $char;
                    break;
                }
            }

            $chars = array_reverse($chars);

            foreach ($chars as $char) {
                if ((int) $char) {
                    $this->numbers[$id] .= $char;
                    break;
                }
            }
        }
    }

    private function loadData02(): void
    {
        foreach ($this->data as $id => $data) {
            $matches = [];
            preg_match_all('/(?=(\d|one|two|three|four|five|six|seven|eight|nine))/', $data, $matches);

            if (count($matches[1]) === 1) {
                $this->numbers[$id] = $this->mapNumber($matches[1][0]);
                $this->numbers[$id] .= $this->mapNumber($matches[1][0]);
            } else {
                $this->numbers[$id] = $this->mapNumber(array_shift($matches[1]));
                $this->numbers[$id] .= $this->mapNumber(array_pop($matches[1]));
            }
        }
    }

    private function mapNumber(string $input): int
    {
        return $this->map[$input] ?? (int) $input;
    }
}
