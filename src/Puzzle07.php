<?php

namespace Shadowinek\Aoc2023;

class Puzzle07 extends AbstractPuzzle
{
    private array $hands = [];
    private array $mappings = [
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'T' => 10,
        'J' => 11,
        'Q' => 12,
        'K' => 13,
        'A' => 14,
    ];

    private const FIVE_OF_KIND = 70000000000;
    private const FOUR_OF_KIND = 60000000000;
    private const FULL_HOUSE = 50000000000;
    private const THREE_OF_KIND = 40000000000;
    private const TWO_PAIRS = 30000000000;
    private const TWO_OF_KIND = 20000000000;
    private const HIGH_CARD = 10000000000;

    public function runPart01(): int
    {
        $this->loadData();

        return $this->calculate();
    }

    public function runPart02(): int
    {
        $this->loadData(true);

        return $this->calculate();
    }

    private function calculate(): int
    {
        $strengths = array_column($this->hands, 'strength');
        array_multisort($strengths, SORT_ASC, $this->hands);
        $total = 0;

        foreach ($this->hands as $rank => $hand) {
            $total += $hand['bid'] * ($rank + 1);
        }

        return $total;
    }

    private function loadData(bool $useJokers = false): void
    {
        if ($useJokers) {
            $this->mappings['J'] = 1;
        }

        foreach ($this->data as $data) {
            $split = explode(' ', $data);

            $cards = str_split($split[0]);

            $this->hands[] = [
                'cards' => $cards,
                'bid' => (int) $split[1],
                'strength' => $this->determineHandStrength($cards, $useJokers) + $this->determineCardsStrength($cards),
            ];
        }
    }

    private function determineCardsStrength(array $cards): int
    {
        $strength = 0;

        foreach ($cards as $id => $card) {
            $strength += $this->mappings[$card] * pow(10, 8 - 2*$id);
        }

        return $strength;
    }

    private function determineHandStrength(array $cards, bool $useJokers = false): int
    {
        $counts = array_count_values($cards);

        if ($useJokers && isset($counts['J'])) {
            $jokers = $counts['J'];
            unset($counts['J']);
        }

        arsort($counts);
        $count = array_shift($counts);

        if ($useJokers) {
            $count += $jokers ?? 0;
        }

        switch ($count) {
            case 5:
                return self::FIVE_OF_KIND;
            case 4:
                return self::FOUR_OF_KIND;
            case 3:
                $subcount = array_shift($counts);

                switch ($subcount) {
                    case 2:
                        return self::FULL_HOUSE;
                    case 1:
                        return self::THREE_OF_KIND;
                    default:
                        // do nothing
                        break;
                }
                break;
            case 2:
                $subcount = array_shift($counts);

                switch ($subcount) {
                    case 2:
                        return self::TWO_PAIRS;
                    case 1:
                        return self::TWO_OF_KIND;
                    default:
                        // do nothing
                        break;
                }
                break;
            case 1:
                return self::HIGH_CARD;
            default:
                // do nothing
                break;
        }
    }
}
