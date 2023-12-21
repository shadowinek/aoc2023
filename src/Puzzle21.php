<?php

namespace Shadowinek\Aoc2023;

class Puzzle21 extends AbstractPuzzle
{
    private array $offsets = [
        [0, 1],
        [1, 0],
        [0, -1],
        [-1, 0],
    ];
    private array $map = [];
    private const PLOT = '.';
    private const ROCK = '#';
    private const START = 'S';
    private string $start;
    private const STEPS = 64;
    private array $neighbours = [];
    private array $reached = [];
    public function runPart01(): int
    {
        $this->loadData();
        $this->calculateNeighbours();

        return $this->calculate();
    }

    private function calculateNeighbours(): void
    {
        foreach ($this->map as $x => $cols) {
            foreach ($cols as $y => $value) {
                if ($value !== self::ROCK) {
                    $this->getNeighbours($x, $y);
                }
            }
        }
    }

    private function getNeighbours(int $x, int $y): void
    {
        $neighbours = [];

        foreach ($this->offsets as $offset) {
            $newX = $x + $offset[0];
            $newY = $y + $offset[1];

            if (isset($this->map[$newX][$newY]) && $this->map[$newX][$newY] !== self::ROCK) {
                $neighbours[] = $this->getKey($x + $offset[0], $y + $offset[1]);
            }
        }

        $this->neighbours[$this->getKey($x, $y)] = $neighbours;
    }

    private function calculate(): int
    {
        $this->reached[$this->start] = true;

        for ($i=0;$i<self::STEPS;$i++) {
            $this->calculateStep();
        }

        return array_sum($this->reached);
    }



    public function runPart02(): int
    {
        $this->loadData();

        return 0;
    }

    private function loadData(): void
    {
        foreach ($this->data as $row => $data) {
            foreach (str_split($data) as $col => $value) {
                $this->map[$row][$col] = $value;

                if ($value === self::START) {
                    $this->start = $this->getKey($row, $col);
                    $this->map[$row][$col] = self::PLOT;
                }
            }
        }
    }

    private function getKey(int $x, int $y): string
    {
        return $x . ':' . $y;
    }

    private function print(): void
    {
        foreach ($this->map as $x => $row) {
            foreach ($row as $y => $col) {
                echo $col;
            }

            echo PHP_EOL;
        }

        echo PHP_EOL;
    }

    private function calculateStep(): void
    {
        $reached = [];

        foreach ($this->reached as $key => $value) {
            foreach ($this->neighbours[$key] as $neighbour) {
                $reached[$neighbour] = true;
            }
        }

        $this->reached = $reached;
    }
}
