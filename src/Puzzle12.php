<?php

namespace Shadowinek\Aoc2023;

class Puzzle12 extends AbstractPuzzle
{
    private array $rows;

    public function runPart01(): int
    {
        $this->loadData();

        return $this->calculate();
    }

    private function calculateValidArrangements(array $row): int
    {
        $arrangements = $row['arrangements'];

        foreach (str_split($row['row']) as $i => $value) {
            foreach ($arrangements as $arrangement) {
                switch ($value) {
                    case '#':
                    case '.':
                        if ($arrangement[$i] !== $value) {
                            unset($arrangements[array_search($arrangement, $arrangements)]);
                        }
                        break;
                    case '?':
                        // do nothing
                        break;
                }
            }
        }

        return count($arrangements);
    }

    private function calculate(): int
    {
        $total = 0;

        foreach ($this->rows as $row) {
//            echo $row['row'] . PHP_EOL;
            $total += $this->calculateValidArrangements($row);
        }

        return $total;
    }

    public function runPart02(): int
    {
        $this->loadData(true);

        return $this->calculate();
    }

    private function loadData(bool $multiple = false): void
    {
        foreach ($this->data as $row => $data) {
            $explode = explode(' ', $data);

            if ($multiple) {
                $explode0 = $explode[0];
                $explode[0] .= '?' . $explode0;
                $explode[0] .= '?' . $explode0;
                $explode[0] .= '?' . $explode0;
                $explode[0] .= '?' . $explode0;

                $explode1 = $explode[1];
                $explode[1] .= ',' . $explode1;
                $explode[1] .= ',' . $explode1;
                $explode[1] .= ',' . $explode1;
                $explode[1] .= ',' . $explode1;
            }

            $length = strlen($explode[0]);
            $groups = explode(',', $explode[1]);

//            echo $explode[0] . ' ' . $explode[1] .  PHP_EOL;

            $this->rows[] = [
                'length' => $length,
                'row' => $explode[0],
                'groups' => $groups,
                'arrangements' => $this->generateArrangements($groups, $length),
            ];
        }
    }

    private function generateArrangements(array $groups, int $length): array
    {
        $arrangements = [];
        $defaultGaps = array_fill(1, count($groups) - 1, 1);
        $defaultGaps[0] = 0;
        $defaultGaps[] = 0;

        $sumGroups = array_sum($groups);
        $diff = $length - $sumGroups - array_sum($defaultGaps);

        if ($diff === 0) {
            $arrangements[] = $this->generateArrangement($groups, $defaultGaps);
        } else {
            $offsets = $this->splitNumber($diff, count($defaultGaps));

            foreach ($offsets as $offset) {
                $gaps = $defaultGaps;

                for ($i=0;$i<count($offset); $i++) {
                    $gaps[$i] += $offset[$i];
                }

                $arrangements[] = $this->generateArrangement($groups, $gaps);
            }
        }

        return $arrangements;

    }

    private function splitNumber(int $number, int $parts): array
    {
        return $this->splitRecursive($number, $parts, []);
    }

    private function splitRecursive($number, $parts, $current): array
    {
        $result = [];

        if ($parts == 1) {
            $current[] = $number;
            $result[] = $current;
            return $result;
        }

        for ($i = 0; $i <= $number; $i++) {
            $newCurrent = $current;
            $newCurrent[] = $i;
            $result = array_merge($result, $this->splitRecursive($number - $i, $parts - 1, $newCurrent));
        }

        return $result;
    }

    private function generateArrangement(array $groups, array $gaps): string
    {
        $string = '';

        foreach ($groups as $i => $group) {
            $string .= str_repeat('.', $gaps[$i]);
            $string .= str_repeat('#', $group);;
        }

        $string .= str_repeat('.', end($gaps));

        return $string;
    }

    private function parseRow(string $input): array
    {
        $matches = [];

        preg_match_all('/([#?]+)/', $input, $matches);

        return $matches[0];
    }
}
