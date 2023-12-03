<?php

namespace Shadowinek\Aoc2023;

class Puzzle02 extends AbstractPuzzle
{
    private const RED = 12;
    private const GREEN = 13;
    private const BLUE = 14;

    private array $map = [
        'red' => self::RED,
        'green' => self::GREEN,
        'blue' => self::BLUE,
    ];

    private array $games = [];
    private array $powers = [];

    public function runPart01(): int
    {
        $this->loadData01();

        return array_sum($this->games);
    }

    public function runPart02(): int
    {
        $this->loadData02();

        return array_sum($this->powers);
    }

    private function loadData02(): void
    {
        foreach ($this->data as $data) {
            $explode = explode(':', $data);
            $game = substr($explode[0], 5);
            $red = $green = $blue = 0;

            $subgames = explode(';', $explode[1]);

            foreach ($subgames as $subgame) {
                $cubes = explode(',', $subgame);

                foreach ($cubes as $cube) {
                    $pull = explode(' ', trim($cube));
                    $color = $pull[1];

                    if ($pull[0] > $$color) {
                        $$color = $pull[0];
                    }
                }
            }

            $this->powers[$game] = $red * $green * $blue;
        }
    }

    private function loadData01(): void
    {
        foreach ($this->data as $data) {
            $explode = explode(':', $data);
            $game = substr($explode[0], 5);
            $possible = true;

            $subgames = explode(';', $explode[1]);

            foreach ($subgames as $subgame) {
                $cubes = explode(',', $subgame);

                foreach ($cubes as $cube) {
                    $pull = explode(' ', trim($cube));

                    if ($pull[0] > $this->map[$pull[1]]) {
                        $possible = false;
                        break;
                    }

                    if (!$possible) {
                        break;
                    }
                }
            }

            if ($possible) {
                $this->games[$game] = $game;
            }
        }
    }
}
