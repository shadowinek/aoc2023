<?php

namespace Shadowinek\Aoc2023;

class Puzzle04 extends AbstractPuzzle
{
    private array $scratchcards = [];
    public function runPart01(): int
    {
        $this->loadData();

        return array_sum(array_column($this->scratchcards, 'points'));
    }

    public function runPart02(): int
    {
        $this->loadData();

        for ($j=1;$j<=count($this->scratchcards);$j++) {
            $card = $this->scratchcards[$j];

            if ($card['count'] > 0) {
                for ($i=1;$i<=$card['count'];$i++) {
                    if (isset($this->scratchcards[$j+$i])) {
                        $this->scratchcards[$j+$i]['copies'] += $card['copies'];
                    }
                }
            }
        }

        return array_sum(array_column($this->scratchcards, 'copies'));
    }
    private function loadData(): void
    {
        foreach ($this->data as $data) {
            $card = explode(':', $data);
            $cardId = trim(substr($card[0], 5));

            $parts = explode('|', trim($card[1]));

            $winningNumbers = $this->parseNumbers($parts[0]);
            $myNumbers = $this->parseNumbers($parts[1]);
            $hits = array_intersect($winningNumbers, $myNumbers);

            $this->scratchcards[$cardId] = [
                'winningNumbers' => $winningNumbers,
                'myNumbers' => $myNumbers,
                'hits' => $hits,
                'count' => count($hits),
                'points' => $this->getPoints(count($hits)),
                'copies' => 1,
            ];
        }
    }

    private function getPoints(int $hits): int
    {
        if ($hits === 0) {
            return 0;
        }

        return pow(2, $hits - 1);
    }
}
