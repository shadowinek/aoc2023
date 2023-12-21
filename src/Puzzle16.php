<?php

namespace Shadowinek\Aoc2023;

class Puzzle16 extends AbstractPuzzle
{
    private array $map = [];
    private array $energized = [];

    private const ON = 1;
    private const OFF = 0;

    private const MIRROR_1 = '\\';
    private const MIRROR_2 = '/';
    private const MIRROR_3 = '|';
    private const MIRROR_4 = '-';
    private const EMPTY = '.';
    private const TYPE_ROW = 0;
    private const TYPE_COL = 1;

    private const DIRECTION_UP = 0;
    private const DIRECTION_RIGHT = 1;
    private const DIRECTION_DOWN = 2;
    private const DIRECTION_LEFT = 3;

    private array $cache = [];

    private array $results = [];

    public function runPart01(): int
    {
        $this->loadData();

        $start = new Laser('0:-1', self::DIRECTION_RIGHT, self::TYPE_ROW);
        $this->laserShow([$start]);

        return max($this->results);
    }

    private function laserShow(array $lasers): void
    {
        /** @var Laser $laser */
        while ($laser = array_shift($lasers)) {
            if ($this->cache[$laser->getHash()] ?? false) {
                continue;
            } else {
                list($x, $y) = explode(':', $laser->start);

                switch ($laser->type) {
                    case self::TYPE_ROW:
                        switch ($laser->direction) {
                            case self::DIRECTION_RIGHT:
                                for ($i=$y+1;$i<count($this->map[$x]);$i++) {
                                    if (isset($this->map[$x][$i])) {
                                        $next = $this->map[$x][$i];
                                        $this->energized[$this->getKey($x, $i)] = self::ON;

                                        switch ($next) {
                                            case self::MIRROR_1:
                                                $lasers[] = new Laser($this->getKey($x, $i), self::DIRECTION_DOWN, self::TYPE_COL);
                                                break 2;
                                            case self::MIRROR_2:
                                                $lasers[] = new Laser($this->getKey($x, $i), self::DIRECTION_UP, self::TYPE_COL);
                                                break 2;
                                            case self::MIRROR_3:
                                                $lasers[] = new Laser($this->getKey($x, $i), self::DIRECTION_UP, self::TYPE_COL);
                                                $lasers[] = new Laser($this->getKey($x, $i), self::DIRECTION_DOWN, self::TYPE_COL);
                                                break 2;
                                            case self::MIRROR_4:
                                            case self::EMPTY:
                                            default:
                                                // do nothing
                                                break;
                                        }
                                    }
                                }
                                break;
                            case self::DIRECTION_LEFT:
                                for ($i=$y-1;$i>=0;$i--) {
                                    if (isset($this->map[$x][$i])) {
                                        $next = $this->map[$x][$i];
                                        $this->energized[$this->getKey($x, $i)] = self::ON;

                                        switch ($next) {
                                            case self::MIRROR_1:
                                                $lasers[] = new Laser($this->getKey($x, $i), self::DIRECTION_UP, self::TYPE_COL);
                                                break 2;
                                            case self::MIRROR_2:
                                                $lasers[] = new Laser($this->getKey($x, $i), self::DIRECTION_DOWN, self::TYPE_COL);
                                                break 2;
                                            case self::MIRROR_3:
                                                $lasers[] = new Laser($this->getKey($x, $i), self::DIRECTION_UP, self::TYPE_COL);
                                                $lasers[] = new Laser($this->getKey($x, $i), self::DIRECTION_DOWN, self::TYPE_COL);
                                                break 2;
                                            case self::MIRROR_4:
                                            case self::EMPTY:
                                            default:
                                                // do nothing
                                                break;
                                        }
                                    }
                                }
                                break;
                            default:
                                // do nothing
                                break;
                        }
                        break;
                    case self::TYPE_COL:
                        switch ($laser->direction) {
                            case self::DIRECTION_DOWN:
                                for ($i=$x+1;$i<count($this->map);$i++) {
                                    if (isset($this->map[$i][$y])) {
                                        $next = $this->map[$i][$y];
                                        $this->energized[$this->getKey($i, $y)] = self::ON;

                                        switch ($next) {
                                            case self::MIRROR_1:
                                                $lasers[] = new Laser($this->getKey($i, $y), self::DIRECTION_RIGHT, self::TYPE_ROW);
                                                break 2;
                                            case self::MIRROR_2:
                                                $lasers[] = new Laser($this->getKey($i, $y), self::DIRECTION_LEFT, self::TYPE_ROW);
                                                break 2;
                                            case self::MIRROR_4:
                                                $lasers[] = new Laser($this->getKey($i, $y), self::DIRECTION_RIGHT, self::TYPE_ROW);
                                                $lasers[] = new Laser($this->getKey($i, $y), self::DIRECTION_LEFT, self::TYPE_ROW);
                                                break 2;
                                            case self::MIRROR_3:
                                            case self::EMPTY:
                                            default:
                                                // do nothing
                                                break;
                                        }
                                    }
                                }
                                break;
                            case self::DIRECTION_UP:
                                for ($i=$x-1;$i>=0;$i--) {
                                    if (isset($this->map[$i][$y])) {
                                        $next = $this->map[$i][$y];
                                        $this->energized[$this->getKey($i, $y)] = self::ON;

                                        switch ($next) {
                                            case self::MIRROR_1:
                                                $lasers[] = new Laser($this->getKey($i, $y), self::DIRECTION_LEFT, self::TYPE_ROW);
                                                break 2;
                                            case self::MIRROR_2:
                                                $lasers[] = new Laser($this->getKey($i, $y), self::DIRECTION_RIGHT, self::TYPE_ROW);
                                                break 2;
                                            case self::MIRROR_4:
                                                $lasers[] = new Laser($this->getKey($i, $y), self::DIRECTION_RIGHT, self::TYPE_ROW);
                                                $lasers[] = new Laser($this->getKey($i, $y), self::DIRECTION_LEFT, self::TYPE_ROW);
                                                break 2;
                                            case self::MIRROR_3:
                                            case self::EMPTY:
                                            default:
                                                // do nothing
                                                break;
                                        }
                                    }
                                }
                                break;
                            default:
                                // do nothing
                                break;
                        }
                        break;
                }

                $this->cache[$laser->getHash()] = true;
            }
        }

        $this->results[] = array_sum($this->energized);
        $this->cache = [];
        $this->energized = array_fill_keys(array_keys($this->energized), self::OFF);
    }

    public function runPart02(): int
    {
        $this->loadData();

        $count = count($this->map);

        for ($i=0;$i<$count;$i++) {
            $laser = new Laser($this->getKey($i, -1), self::DIRECTION_RIGHT, self::TYPE_ROW);
            $this->laserShow([$laser]);

            $laser = new Laser($this->getKey($i, $count+1), self::DIRECTION_LEFT, self::TYPE_ROW);
            $this->laserShow([$laser]);

            $laser = new Laser($this->getKey(-1, $i), self::DIRECTION_DOWN, self::TYPE_COL);
            $this->laserShow([$laser]);

            $laser = new Laser($this->getKey($count+1, $i), self::DIRECTION_UP, self::TYPE_COL);
            $this->laserShow([$laser]);
        }

        return max($this->results);
    }

    private function loadData(): void
    {
        foreach ($this->data as $row => $data) {
            foreach (str_split($data) as $col => $char) {
                $this->map[$row][$col] = $char;
                $this->energized[$this->getKey($row, $col)] = self::OFF;
            }
        }
    }

    private function getKey(int $x, int $y): string
    {
        return $x . ':' . $y;
    }

    private function print(): void
    {
        foreach ($this->map as $x => $row) {
            foreach ($row as $y => $col) {
                if ($this->energized[$this->getKey($x, $y)] === self::ON) {
                    echo '#';
                } else {
                    echo $col;
                }
            }

            echo PHP_EOL;
        }

        echo PHP_EOL;
    }
}

class Laser {
    public function __construct(public string $start, public int $direction, public int $type) {

    }

    public function getHash(): string
    {
        return $this->start . '#' . $this->direction . '#' . $this->type;
    }
}
