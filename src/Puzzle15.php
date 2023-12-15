<?php

namespace Shadowinek\Aoc2023;

class Puzzle15 extends AbstractPuzzle
{
    private array $strings = [];
    private array $boxes = [];
    public function runPart01(): int
    {
        $this->loadData();

        return $this->calculate();
    }

    private function calculate(): int
    {
        $total = 0;

        foreach ($this->strings as $string) {
            $total += $this->hash($string);
        }

        return $total;
    }

    private function hash(string $string): int
    {
        $value = 0;
        $chars = str_split($string);

        foreach ($chars as $char) {
            $ascii = ord($char);
            $value += $ascii;
            $value *= 17;
            $value = $value % 256;
        }

        return $value;
    }


    public function runPart02(): int
    {
        $this->loadData();
        $this->sortLenses();

        return $this->calculateBoxes();
    }

    private function calculateBoxes(): int
    {
        $total = 0;

        foreach ($this->boxes as $i => $box) {
            $j = 1;
            foreach ($box as $lens) {
                $total += ($i+1) * $lens['value'] * $j;
                $j++;
            }
        }

        return $total;
    }

    private function sortLenses(): void
    {
        foreach ($this->strings as $string) {
            if (str_contains($string, '-')) {
                $label = str_replace('-', '', $string);
                $hash = $this->hash($label);
                $this->removeLens($hash, $label);
            } elseif (str_contains($string, '=')) {
                list($label, $value) = explode('=', $string);
                $hash = $this->hash($label);
                $this->addLens($hash, $label, $value);
            }
        }
    }

    private function removeLens(int $box, string $label): void
    {
        foreach ($this->boxes[$box] ?? [] as $i => $boxLens) {
            if ($boxLens['label'] === $label) {
                unset($this->boxes[$box][$i]);
            }
        }
    }

    private function addLens(int $box, string $label, int $value): void
    {
        foreach ($this->boxes[$box] ?? [] as $i => $boxLens) {
            if ($boxLens['label'] === $label) {
                $this->boxes[$box][$i]['value'] = $value;
                return;
            }
        }

        $this->boxes[$box][] = [
            'label' => $label,
            'value' => $value,
        ];
    }

    private function loadData(): void
    {
        $this->strings = explode(',', $this->data[0]);
    }
}
