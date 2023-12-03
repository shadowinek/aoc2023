<?php

namespace Shadowinek\Aoc2023;

class Puzzle03 extends AbstractPuzzle
{
    private array $numbers = [];
    private array $symbols = [];
    private array $lifts = [];

    private array $gears = [];

    private array $accept = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
    ];

    private array $special = [
        '/', '*', '#', '=', '@', '+', '-', '&', '$', '%',
    ];

    public function runPart01(): int
    {
        $this->loadData01();
        $this->calculate01();

        return array_sum($this->lifts);
    }

    public function runPart02(): int
    {
        $this->loadData02();
        $this->calculate02();

        $ratios = [];

        foreach ($this->gears as $gear) {
            if (count($gear) === 2) {
                $ratios[] = $gear[0] * $gear[1];
            }
        }

        return array_sum($ratios);
    }

    private function calculate02(): void
    {
        $maxI = count($this->numbers);
        $maxJ = count($this->numbers[0]);

        for ($i=0;$i<$maxI; $i++) {
            $found = '';
            $toCheck = [];

            for ($j=0; $j<$maxJ; $j++) {
                $digit = $this->numbers[$i][$j];

                if ($digit !== false) {
                    $found .= $digit;
                    $toCheck[] = [$i, $j];
                } else {
                    if (!empty($found)) {
                        $gear = $this->check02($toCheck);

                        if ($gear) {
//                            echo "Found number: $found\n";

                            $this->gears[$gear[0] . '-' . $gear[1]][] = $found;
                        } else {
//                            echo "Not found number: $found\n";
                        }

                        $found = '';
                        $toCheck = [];
                    }
                }
            }
        }
    }

    private function calculate01(): void
    {
        $maxI = count($this->numbers);
        $maxJ = count($this->numbers[0]);

        for ($i=0;$i<$maxI; $i++) {
            $found = '';
            $toCheck = [];

            for ($j=0; $j<$maxJ; $j++) {
                $digit = $this->numbers[$i][$j];

                if ($digit !== false) {
                    $found .= $digit;
                    $toCheck[] = [$i, $j];
                } else {
                    if (!empty($found)) {
                        if ($this->check01($toCheck)) {
//                            echo "Found number: $found\n";
                            $this->lifts[] = $found;
                        } else {
//                            echo "Not found number: $found\n";
                        }

                        $found = '';
                        $toCheck = [];
                    }
                }
            }
        }
    }

    private function check01(array $toCheck): bool
    {
        foreach ($toCheck as $coords) {
            $adjacentOffsets = [
                [-1, -1], [-1, 0], [-1, 1],
                [0, -1], [0, 1],
                [1, -1], [1, 0], [1, 1]
            ];

            foreach ($adjacentOffsets as $offset) {
                $di = $coords[0] + $offset[0];
                $dj = $coords[1] + $offset[1];

                if (isset($this->symbols[$di][$dj]) && $this->symbols[$di][$dj]) {
                    return true;
                }
            }
        }

        return false;
    }

    private function check02(array $toCheck): array|bool
    {
        foreach ($toCheck as $coords) {
            $adjacentOffsets = [
                [-1, -1], [-1, 0], [-1, 1],
                [0, -1], [0, 1],
                [1, -1], [1, 0], [1, 1]
            ];

            foreach ($adjacentOffsets as $offset) {
                $di = $coords[0] + $offset[0];
                $dj = $coords[1] + $offset[1];

                if (isset($this->symbols[$di][$dj]) && $this->symbols[$di][$dj]) {
                    return [$di, $dj];
                }
            }
        }

        return false;
    }

    private function loadData01(): void
    {
        foreach ($this->data as $x => $data) {
            $chars = str_split($data . 'n');

            foreach ($chars as $y => $char) {
                if ($char === '.') {
                    $this->symbols[$x][$y] = false;
                    $this->numbers[$x][$y] = false;
                } elseif (in_array($char, $this->accept)) {
                    $this->symbols[$x][$y] = false;
                    $this->numbers[$x][$y] = $char;
                } elseif (in_array($char, $this->special)) {
                    $this->symbols[$x][$y] = true;
                    $this->numbers[$x][$y] = false;
                } else {
                    $this->symbols[$x][$y] = false;
                    $this->numbers[$x][$y] = false;
                }
            }
        }
    }

    private function loadData02(): void
    {
        foreach ($this->data as $x => $data) {
            $chars = str_split($data . 'n');

            foreach ($chars as $y => $char) {
                if ($char === '.') {
                    $this->symbols[$x][$y] = false;
                    $this->numbers[$x][$y] = false;
                } elseif (in_array($char, $this->accept)) {
                    $this->symbols[$x][$y] = false;
                    $this->numbers[$x][$y] = $char;
                } elseif ($char === '*') {
                    $this->symbols[$x][$y] = true;
                    $this->numbers[$x][$y] = false;
                } else {
                    $this->symbols[$x][$y] = false;
                    $this->numbers[$x][$y] = false;
                }
            }
        }
    }
}
