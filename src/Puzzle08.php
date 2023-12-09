<?php

namespace Shadowinek\Aoc2023;

class Puzzle08 extends AbstractPuzzle
{
    private array $directions = [];
    private array $nodes = [];

    private array $starts = [];

    private array $turns = [
        'L' => self::LEFT,
        'R' => self::RIGHT,
    ];

    private const START = 'AAA';
    private const END = 'ZZZ';
    private const LEFT = 0;
    private const RIGHT = 1;


    public function runPart01(): int
    {
        $this->loadData();
        $steps = 0;
        $currentNode = self::START;

        while (true) {
            foreach ($this->directions as $direction) {
                $steps++;

                $currentNode = $this->nodes[$currentNode][$this->turns[$direction]];

                if ($currentNode === self::END) {
                    return $steps;
                }
            }
        }
    }

    public function runPart02(): int
    {
        $this->loadData();
        $steps = 0;
        $currentNodes = $this->starts;
        $ends = [];

        while (true) {
            foreach ($this->directions as $direction) {
                $steps++;
                $newNodes = [];

                foreach ($currentNodes as $currentNode) {
                    $newNode = $this->nodes[$currentNode][$this->turns[$direction]];

                    if ($this->isEnd($newNode)) {
                        $ends[$newNode] = $steps;
                    } else {
                        $newNodes[] = $newNode;
                    }
                }

                if (empty($newNodes)) {
                    break 2;
                } else {
                    $currentNodes = $newNodes;
                }
            }
        }

        return $this->findLCM(array_values($ends));
    }

    private function loadData(): void
    {
        $data = $this->data;

        $this->directions = str_split(array_shift($data));

        foreach ($data as $row) {
            if (!empty($row)) {
                $parsed = $this->parseRow($row);
                $this->nodes[$parsed[0]] = [$parsed[1], $parsed[2]];

                if ($this->isStart($parsed[0])) {
                    $this->starts[] = $parsed[0];
                }
            }
        }
    }

    private function parseRow(string $input): array
    {
        $numbers = [];

        preg_match_all('/[A-Z0-9]{3}/', $input, $numbers);

        return $numbers[0];
    }

    private function isStart(string $node): bool
    {
        return str_ends_with($node, 'A');
    }

    private function isEnd(string $node): bool
    {
        return str_ends_with($node, 'Z');
    }


    /**
     * Greatest Common Divisor
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    private function findGCD(int $a, int $b): int
    {
        if ($b === 0) {
            return $a;
        }

        return $this->findGCD($b, $a % $b);
    }

    /**
     * Lowest Common Multiple
     *
     * @param array $numbers
     * @return int
     */
    function findLCM(array $numbers): int
    {
        $count = count($numbers);
        $lcm = $numbers[0];

        for ($i = 1; $i < $count; $i++) {
            $lcm = ((($numbers[$i] * $lcm)) / ($this->findGCD($numbers[$i], $lcm)));
        }

        return $lcm;
    }
}
