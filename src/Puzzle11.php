<?php

namespace Shadowinek\Aoc2023;

class Puzzle11 extends AbstractPuzzle
{
    private array $map = [];
    private array $galaxiesRow = [];
    private array $galaxiesCol = [];
    private array $emptyRow = [];
    private array $emptyCol = [];

    private array $galaxies = [];
    private const GALAXY = '#';
    private const PART_01 = 1;
    private const PART_02 = 1000000;

    public function runPart01(): int
    {
        $this->loadData();

        $this->expand(self::PART_01);

        return $this->calculate();
    }

    private function calculate(): int
    {
        $dist = 0;

        while ($galaxy = array_shift($this->galaxies)) {
            $x = $galaxy[0];
            $y = $galaxy[1];

            foreach ($this->galaxies as $secondGalaxy) {
                $dist += abs($x - $secondGalaxy[0]) + abs($y - $secondGalaxy[1]);
            }
        }

        return $dist;
    }

    private function expand(int $expand): void
    {
        foreach ($this->galaxies as $id => $galaxy) {
            $expandX = $expandY = 0;
            $x = $galaxy[0];
            $y = $galaxy[1];

            foreach ($this->emptyRow as $row => $zero) {
                if ($row < $x) {
                    $expandX++;
                }
            }

            foreach ($this->emptyCol as $col => $zero) {
                if ($col < $y) {
                    $expandY++;
                }
            }

            $this->galaxies[$id] = [$x + $expandX * $expand, $y + $expandY * $expand];
        }
    }

    public function runPart02(): int
    {
        $this->loadData();

        $this->expand(self::PART_02 - 1);

        return $this->calculate();
    }

    private function loadData(): void
    {
        foreach ($this->data as $row => $data) {
            $cols = str_split($data);

            foreach ($cols as $col => $value) {
                $this->map[$row][$col] = $value;

                if ($value === self::GALAXY) {
                    $this->galaxies[] = [$row, $col];

                    if (isset($this->galaxiesRow[$row])) {
                        $this->galaxiesRow[$row]++;
                    } else {
                        $this->galaxiesRow[$row] = 1;
                    }

                    if (isset($this->galaxiesCol[$col])) {
                        $this->galaxiesCol[$col]++;
                    } else {
                        $this->galaxiesCol[$col] = 1;
                    }
                }
            }
        }

        for ($i=0;$i<count($this->data);$i++) {
            if (!isset($this->galaxiesRow[$i])) {
                $this->emptyRow[$i] = 0;
            }

            if (!isset($this->galaxiesCol[$i])) {
                $this->emptyCol[$i] = 0;
            }
        }
    }
}
