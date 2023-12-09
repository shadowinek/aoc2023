<?php

namespace Shadowinek\Aoc2023;

class Puzzle09 extends AbstractPuzzle
{
    /**
     * @var Numbers[]
     */
    private array $rows = [];

    public function runPart01(): int
    {
        $this->loadData();

        return $this->calculate();
    }

    private function calculate(): int
    {
        $total = 0;

        foreach ($this->rows as $row) {
            $total += $this->calculateNext($row);
        }

        return $total;
    }

    private function calculateNext(Numbers $numbers): int|bool
    {
        if ($numbers->isEnd()) {
            return false;
        } else {
            $next = $this->calculateNext($numbers->diffs);

            if ($next !== false) {
                return $numbers->getLast() + $next;
            } else {
                return $numbers->getLast() + $numbers->getDiff();
            }
        }
    }

    public function runPart02(): int
    {
        $this->loadData(true);

        return $this->calculate();
    }

    private function loadData(bool $reverse = false): void
    {
        foreach ($this->data as $data) {
            $numbers = $this->parseNumbers($data);

            if ($reverse) {
                $numbers = array_reverse($numbers);
            }

            $this->rows[] = new Numbers($numbers);
        }

        foreach ($this->rows as $row) {
            while (!$row->isEnd()) {
                $diffs = new Numbers($this->calculateDiffs($row->numbers));
                $row->diffs = $diffs;
                $row = $diffs;
            }
        }
    }

    private function calculateDiffs(array $numbers): array
    {
        $diffs = [];
        for ($i = 0; $i < count($numbers) - 1; $i++) {
            $diffs[] = $numbers[$i + 1] - $numbers[$i];
        }

        return $diffs;
    }
}

class Numbers
{
    public Numbers $diffs;

    public function __construct(public readonly array $numbers)
    {
    }

    public function isEnd(): bool
    {
        return empty(array_filter($this->numbers));
    }

    public function getLast(): int
    {
        return $this->numbers[count($this->numbers) - 1];
    }

    public function getDiff(): int
    {
        return $this->numbers[count($this->numbers) - 1] - $this->numbers[count($this->numbers) - 2];
    }
}
