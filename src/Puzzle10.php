<?php

namespace Shadowinek\Aoc2023;

class Puzzle10 extends AbstractPuzzle
{
    private const NORTH = 'NORTH';
    private const EAST = 'EAST';
    private const SOUTH = 'SOUTH';
    private const WEST = 'WEST';

    private const LOOP = 2;
    private const ENCLOSED = 1;
    private const OPEN = 0;

    private const NOT_DEFINED = -1;
    private array $map = [];
    private array $connectedMap = [];
    private array $mappings = [
        self::TYPE_VERTICAL => [
            self::NORTH => true,
            self::EAST => false,
            self::SOUTH => true,
            self::WEST => false,
        ],
        self::TYPE_HORIZONTAL => [
            self::NORTH => false,
            self::EAST => true,
            self::SOUTH => false,
            self::WEST => true,
        ],
        self::TYPE_L => [
            self::NORTH => true,
            self::EAST => true,
            self::SOUTH => false,
            self::WEST => false,
        ],
        self::TYPE_J => [
            self::NORTH => true,
            self::EAST => false,
            self::SOUTH => false,
            self::WEST => true,
        ],
        self::TYPE_7 => [
            self::NORTH => false,
            self::EAST => false,
            self::SOUTH => true,
            self::WEST => true,
        ],
        self::TYPE_F => [
            self::NORTH => false,
            self::EAST => true,
            self::SOUTH => true,
            self::WEST => false,
        ],
        self::TYPE_DOT => [
            self::NORTH => false,
            self::EAST => false,
            self::SOUTH => false,
            self::WEST => false,
        ],
        self::TYPE_START => [
            self::NORTH => true,
            self::EAST => true,
            self::SOUTH => true,
            self::WEST => true,
        ],
    ];
    private Node $start;

    private const TYPE_HORIZONTAL = '-';
    private const TYPE_VERTICAL = '|';
    private const TYPE_L = 'L';
    private const TYPE_J = 'J';
    private const TYPE_7 = '7';
    private const TYPE_F = 'F';
    private const TYPE_DOT = '.';

    private const TYPE_START = 'S';
    private array $arealMap = [];
    private array $mapOpposite = [
        self::NORTH => self::SOUTH,
        self::EAST => self::WEST,
        self::SOUTH => self::NORTH,
        self::WEST => self::EAST,
    ];

    public function runPart01(): int
    {
        $this->loadData();
        $this->connect();

        return $this->calculate();
    }

    private function defineLoop(): void
    {
        $toCheck = [$this->start];
        $step = 0;
        $lastDirection = null;

        while ($currentNode = array_shift($toCheck)) {
            $this->arealMap[$currentNode->row][$currentNode->col] = self::LOOP;
            $currentNode->step = $step++;
            $currentNode->loop = true;

            if ($currentNode->start) {
                $currentNode->type = $this->findType($currentNode);
            }

            if ($lastDirection) {
                unset($currentNode->connections[$this->mapOpposite[$lastDirection]]);
            }

            foreach ($currentNode->connections as $direction => $node) {
                $lastDirection = $direction;
                if (!$node->start) {
                    $toCheck[] = $node;
                }
                break;
            }
        }
    }

    private function calculate(): int
    {
        $count = 1;
        $nodes = [];

        foreach ($this->start->connections as $direction => $node) {
            $nodes[] = [
                'direction' => $direction,
                'node' => $node,
            ];
        }

        $first = $nodes[0]['node'];
        $firstDirection = $nodes[0]['direction'];
        $second = $nodes[1]['node'];
        $secondDirection = $nodes[1]['direction'];

        while (1) {
            if ($first->row === $second->row && $first->col === $second->col) {
                return $count;
            }

            unset($first->connections[$this->mapOpposite[$firstDirection]]);
            foreach ($first->connections as $direction => $node) {
                $first = $node;
                $firstDirection = $direction;
            }

            unset($second->connections[$this->mapOpposite[$secondDirection]]);
            foreach ($second->connections as $direction => $node) {
                $second = $node;
                $secondDirection = $direction;
            }

            $count++;
        }
    }

    private function connect(): void
    {
        $toCheck = [$this->start];
        $this->arealMap[$this->start->row][$this->start->col] = self::LOOP;

        while ($currentNode = array_shift($toCheck)) {
            foreach ($currentNode->openings as $direction => $opening) {
                if ($opening) {
                    switch ($direction) {
                        case self::NORTH:
                            $row = $currentNode->row - 1;
                            $col = $currentNode->col;
                            break;
                        case self::EAST:
                            $row = $currentNode->row;
                            $col = $currentNode->col + 1;
                            break;
                        case self::SOUTH:
                            $row = $currentNode->row + 1;
                            $col = $currentNode->col;
                            break;
                        case self::WEST:
                            $row = $currentNode->row;
                            $col = $currentNode->col - 1;
                            break;
                        default:
                            $row = $col = -1;
                            break;
                    }

                    if ($row >= 0 && $col >= 0) {
                        $node = $this->map[$row][$col];
                        $check = $this->mapOpposite[$direction];

                        if ($node->openings[$check]) {
                            $currentNode->connections[$direction] = $node;
                            $node->connections[$check] = $currentNode;

                            if (!isset($this->connectedMap[$node->id])) {
                                $toCheck[] = $node;
                                $this->connectedMap[$node->id] = $node;
                            }
                        }
                    }
                }
            }
        }
    }

    private function calculateWindingRule(): void
    {
        foreach ($this->map as $row => $cols) {
            $wind = 0;
            foreach ($cols as $col => $node) {
                $belowNode = $this->map[$row + 1][$col] ?? null;
                if ($belowNode && $node->loop && $belowNode->loop) {
                    $diff = $node->step - $belowNode->step;

                    if ($node->start && abs($diff) !== 1) {
                        $diff = 1;
                    }

                    if (abs($diff) === 1) {
                        $wind += $diff;
                    }
                }

                if ($this->arealMap[$row][$col] !== self::LOOP) {
                    if ($wind !== 0) {
                        $this->arealMap[$row][$col] = self::ENCLOSED;
                    } else {
                        $this->arealMap[$row][$col] = self::OPEN;
                    }
                }
            }
        }
    }

    public function runPart02(): int
    {
        $this->loadData();
        $this->connect();
        $this->defineLoop();
        $this->calculateWindingRule();

        $this->printArealMap2();

        $enclosed = 0;

        foreach ($this->arealMap as $row) {
            $counts = array_count_values($row);
            $enclosed += $counts[self::ENCLOSED] ?? 0;
        }

        return $enclosed;
    }

    private function loadData(): void
    {
        foreach ($this->data as $row => $data) {
            $nodes = str_split($data);

            foreach ($nodes as $col => $node) {
                $this->map[$row][$col] = new Node($row . '-' . $col, $row, $col, $node, $this->mappings[$node]);
                $this->arealMap[$row][$col] = self::NOT_DEFINED;

                if ($node === self::TYPE_START) {
                    $this->start = $this->map[$row][$col];
                    $this->map[$row][$col]->start = true;
                }
            }
        }
    }

    private function printArealMap(): void
    {
        foreach ($this->arealMap as $row) {
            foreach ($row as $col) {
                echo $col < self::ENCLOSED ? '.' : $col;
            }

            echo PHP_EOL;
        }

        echo PHP_EOL;
    }

    private function printArealMap2(): void
    {
        foreach ($this->arealMap as $rowId => $row) {
            foreach ($row as $colId => $col) {
                $node = $this->map[$rowId][$colId];
                if ($node->loop) {
                    switch ($node->type) {
                        case self::TYPE_VERTICAL:
                            echo '│';
                            break;
                        case self::TYPE_HORIZONTAL:
                            echo '─';
                            break;
                        case self::TYPE_L:
                            echo '└';
                            break;
                        case self::TYPE_J:
                            echo '┘';
                            break;
                        case self::TYPE_7:
                            echo '┐';
                            break;
                        case self::TYPE_F:
                            echo '┌';
                            break;
                        default:
                            break;
                    }
                } else {
                    echo $col < self::ENCLOSED ? '.' : '*';
                }
            }

            echo PHP_EOL;
        }

        echo PHP_EOL;
    }

    private function findType(Node $node): string
    {
        if (isset($node->connections[self::NORTH])) {
            if (isset($node->connections[self::SOUTH])) {
                return self::TYPE_VERTICAL;
            } elseif (isset($node->connections[self::EAST])) {
                return self::TYPE_L;
            } elseif (isset($node->connections[self::WEST])) {
                return self::TYPE_J;
            }
        } elseif (isset($node->connections[self::SOUTH])) {
            if (isset($node->connections[self::EAST])) {
                return self::TYPE_F;
            } elseif (isset($node->connections[self::WEST])) {
                return self::TYPE_7;
            }
        } elseif (isset($node->connections[self::EAST])) {
            return self::TYPE_HORIZONTAL;
        }
    }
}

class Node
{
    public array $connections = [];
    public bool $start = false;
    public bool $loop = false;

    public ?int $step = null;
    public function __construct(
        public string $id,
        public string $row,
        public string $col,
        public string $type,
        public array $openings = []
    ) {
    }
}
