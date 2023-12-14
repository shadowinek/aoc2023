<?php

namespace Shadowinek\Aoc2023;

class Puzzle14 extends AbstractPuzzle
{
    private const ASH = '.';
    private const ROCK = '#';

    private const MOVABLE_ROCK = 'O';

    private array $rows = [];
    private const NORTH = 0;
    private const EAST = 1;
    private const SOUTH = 2;
    private const WEST = 3;
    private const CYCLES = 1000000000;
    private array $cache = [];
    private array $rowCache = [];

    public function runPart01(): int
    {
        $this->loadData();
        $this->shiftRocks(self::NORTH);

        return $this->calculate();
    }

    private function calculate(): int
    {
        $total = 0;
        $rows = $this->rows;

        foreach ($rows as $data) {
            $cols = array_reverse(str_split($data));

            foreach ($cols as $col => $value) {
                if ($value === self::MOVABLE_ROCK) {
                    $total += $col + 1;
                }
            }
        }

        return $total;
    }

    private function shiftRocks(int $direction): void
    {
        switch ($direction) {
            case self::NORTH:
            case self::WEST:
                $search = self::ASH . self::MOVABLE_ROCK;
                $replace = self::MOVABLE_ROCK . self::ASH;
                break;
            case self::SOUTH:
            case self::EAST:
                $search = self::MOVABLE_ROCK . self::ASH;
                $replace = self::ASH . self::MOVABLE_ROCK;
                break;
            default:
                $search = $replace = '';
                break;
        }

        foreach ($this->rows as $id => $row) {
            $replaced = true;

            while ($replaced) {
                $this->rows[$id] = str_replace($search, $replace, $this->rows[$id], $replaced);
            }
        }
    }

    public function runPart02(): int
    {
        $this->loadData();

        $loop = $this->getLoop();

        $modulo = (self::CYCLES - $loop['i']) % $loop['diff'];
        $rest = $modulo + $loop['cache'];

        $this->rows = $this->rowCache[$rest];

        return $this->calculate();
    }

    private function getLoop(): array
    {
        for ($i=1;$i<=self::CYCLES;$i++) {
            $this->doCycle();

            $key = $this->getString();

            $this->rowCache[$i] = $this->rows;

            if (isset($this->cache[$key])) {
                return [
                    'i' => $i,
                    'cache' => $this->cache[$key],
                    'diff' => $i - $this->cache[$key],
                ];
            }

            $this->cache[$key] = $i;
        }

        return [];
    }

    private function doCycle(): void
    {
        $this->shiftRocks(self::NORTH);
        $this->switchRowsAndCols();

        $this->shiftRocks(self::WEST);
        $this->switchRowsAndCols();

        $this->shiftRocks(self::SOUTH);
        $this->switchRowsAndCols();

        $this->shiftRocks(self::EAST);
        $this->switchRowsAndCols();
    }

    private function loadData(): void
    {
        foreach ($this->data as $data) {
            $cols = str_split($data);

            foreach ($cols as $col => $value) {
                if (isset($this->rows[$col])) {
                    $this->rows[$col] .= $value;
                } else {
                    $this->rows[$col] = $value;
                }
            }
        }
    }

    private function switchRowsAndCols(): void
    {
        $newRows = [];

        foreach ($this->rows as $data) {
            $cols = str_split($data);
            foreach ($cols as $col => $value) {
                if (isset($newRows[$col])) {
                    $newRows[$col] .= $value;
                } else {
                    $newRows[$col] = $value;
                }
            }
        }

        $this->rows = $newRows;
    }

    private function getString(): string
    {
        return implode('', $this->rows);
    }
}
