<?php

namespace Shadowinek\Aoc2023;

class Puzzle13 extends AbstractPuzzle
{
    private array $islands;
    private array $islandsStrings;
    private array $rotatedIslands;
    private array $rotatedIslandsStrings;
    private const ASH = '.';
    private const ROCK = '#';

    private int $bitDiffers = 0;

    private array $mapping = [
        self::ASH => 0,
        self::ROCK => 1,
    ];
    public function runPart01(): int
    {
        $this->loadData();

        return $this->calculate();
    }

    private function calculate(): int
    {
        $total = 0;

        foreach ($this->islandsStrings as $id => $island) {
            $a = $this->validate($island, $this->bitDiffers);
            $total += $a * 100;
        }

        foreach ($this->rotatedIslandsStrings as $id => $island) {
            $a = $this->validate($island, $this->bitDiffers);
            $total += $a;
        }

        return $total;
    }

    private function validate(array $island, int $smudges): int
    {
        $length = count($island);

        for ($i=1;$i<$length;$i++) {
            $totalSmudges = $this->compare(
                array_reverse(array_slice($island, 0, $i)),
                array_slice($island, $i)
            );

            if ($smudges === $totalSmudges) {
                return $i;
            }
        }

        return 0;
    }

    private function compare(array $part1, array $part2): int
    {
        $smudges = 0;

        foreach ($part1 as $id => $row) {
            if (!isset($part2[$id])) {
                break;
            }

            $diff = $this->countDifferentBits($row, $part2[$id]);
            if ($diff > $this->bitDiffers) {
                return -1;
            } else {
                $smudges += $diff;
            }
        }

        return $smudges;
    }

    private function countDifferentBits($binaryNum1, $binaryNum2) {
        // Convert binary strings to decimal
        $decimalNum1 = bindec($binaryNum1);
        $decimalNum2 = bindec($binaryNum2);

        // Perform XOR operation to find differing bits
        $xorResult = $decimalNum1 ^ $decimalNum2;

        return array_sum(str_split(decbin($xorResult)));
    }

    public function runPart02(): int
    {
        $this->loadData();

        $this->bitDiffers = 1;

        return $this->calculate();
    }

    private function loadData(): void
    {
        $i = 0;
        $x = 0;
        foreach ($this->data as $data) {
            if (empty($data)) {
                $i++;
                $x=0;
                continue;
            }

            $cols = str_split($data);

            foreach ($cols as $col => $value) {
                $this->islands[$i][$x][$col] = $this->mapping[$value];
                $this->rotatedIslands[$i][$col][$x] = $this->mapping[$value];
            }

            $x++;
        }

        foreach ($this->islands as $id => $island) {
            foreach ($island as $row) {
                $this->islandsStrings[$id][] = implode('', $row);
            }

            foreach ($this->rotatedIslands[$id] as $row) {
                $this->rotatedIslandsStrings[$id][] = implode('', $row);
            }
        }
    }
}
