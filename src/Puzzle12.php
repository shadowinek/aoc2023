<?php

namespace Shadowinek\Aoc2023;

/**
 * Solved with the help of https://www.reddit.com/r/adventofcode/comments/18hbbxe/2023_day_12python_stepbystep_tutorial_with_bonus/
 */
class Puzzle12 extends AbstractPuzzle
{
    private array $rows;
    private const DOT = '.';
    private const HASH = '#';
    private const QUESTION = '?';
    private array $cache = [];

    public function runPart01(): int
    {
        $this->loadData();

        return $this->calculate();
    }

    private function hash(string $record, int $nextGroup, array $groups): int
    {
        $this_group = substr($record, 0, $nextGroup);
        $this_group = str_replace(self::QUESTION, self::HASH, $this_group);

        if ($this_group !== str_repeat(self::HASH, $nextGroup)) {
            return 0;
        }

        if (strlen($record) === $nextGroup) {
            if (count($groups) === 1) {
                return 1;
            } else {
                return 0;
            }
        }

        if (isset($record[$nextGroup]) && ($record[$nextGroup] === self::QUESTION || $record[$nextGroup] === self::DOT)) {
            return $this->calc(substr($record, $nextGroup + 1), array_slice($groups, 1));
        }

        return 0;
    }

    private function calc(string $record, array $groups): int
    {
        $key = $this->getKey($record, $groups);

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        if (empty($groups)) {
            if (!str_contains($record, self::HASH)) {
                return 1;
            } else {
                return 0;
            }
        }

        if (empty($record)) {
            return 0;
        }

        $nextCharacter = $record[0];
        $nextGroup = $groups[0];

        $out = match ($nextCharacter) {
            self::DOT => $this->dot($record, $groups),
            self::HASH => $this->hash($record, $nextGroup, $groups),
            self::QUESTION => $this->dot($record, $groups) + $this->hash($record, $nextGroup, $groups),
            default => 0,
        };

        $this->cache[$key] = $out;

        return $out;
    }

    private function dot(string $record, array $groups): int
    {
        return $this->calc(substr($record, 1), $groups);
    }

    private function getKey(string $record, array $groups): string
    {
        return $record . implode('-', $groups);
    }

    private function calculate(): int
    {
        $total = 0;

        foreach ($this->rows as $row) {
            $total += $this->calc($row['string'], $row['groups']);
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
            list($string, $groups) = explode(' ', $data);

            if ($multiple) {
                $string = "$string?$string?$string?$string?$string";
                $groups = "$groups,$groups,$groups,$groups,$groups";
            }

            $this->rows[] = [
                'string' => $string,
                'groups' => explode(',', $groups),
            ];
        }
    }
}
