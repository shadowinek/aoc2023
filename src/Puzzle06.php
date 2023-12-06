<?php

namespace Shadowinek\Aoc2023;

class Puzzle06 extends AbstractPuzzle
{
    private array $races = [];

    public function runPart01(): int
    {
        $this->loadData();

        return $this->calculate();
    }

    public function runPart02(): int
    {
        $this->loadData2();

        return $this->calculate();
    }

    private function calculate(): int
    {
        $myRecords = 1;

        foreach ($this->races as $race) {
            $better = 0;

            for ($i=0;$i<$race['duration'];$i++) {
                $distance = $i * ($race['duration'] - $i);

                if ($distance > $race['record']) {
                    $better++;
                }
            }

            $myRecords *= $better;
        }

        return $myRecords;
    }

    private function loadData(): void
    {
        $durations = $this->parseNumbers($this->data[0]);
        $records = $this->parseNumbers($this->data[1]);

        foreach ($durations as $id => $duration) {
            $this->races[$id] = [
                'duration' => $duration,
                'record' => $records[$id],
            ];
        }
    }

    private function loadData2(): void
    {
        $durations = $this->parseNumbers(str_replace(' ', '',$this->data[0]));
        $records = $this->parseNumbers(str_replace(' ', '',$this->data[1]));

        foreach ($durations as $id => $duration) {
            $this->races[$id] = [
                'duration' => $duration,
                'record' => $records[$id],
            ];
        }
    }
}
