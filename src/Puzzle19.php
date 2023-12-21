<?php

namespace Shadowinek\Aoc2023;

class Puzzle19 extends AbstractPuzzle
{
    private array $rules = [];
    private array $input = [];

    private const BIGGER_THAN = '>';
    private const SMALLER_THAN = '<';
    private const ACCEPTED = 'A';
    private const REJECTED = 'R';

    private array $accepted = [];
    private array $rejected = [];

    private const INPUT_RULE = 'in';
    public function runPart01(): int
    {
        $this->loadData();

        return $this->calculate();
    }

    private function calculate(): int
    {
        foreach ($this->input as $input) {
            $this->calculateInput($input, self::INPUT_RULE);
        }

        $total = 0;

        foreach ($this->accepted as $item) {
            $total += array_sum($item);
        }

        return $total;
    }

    private function calculateInput(array $input, string $rule): void
    {
        foreach ($this->rules[$rule] as $condition) {
            $operation = $condition['operation'] ?? null;
            $destination = $condition['destination'];

            switch ($operation) {
                case self::BIGGER_THAN:
                    if ($input[$condition['variable']] > $condition['amount']) {
                        if ($destination === self::ACCEPTED) {
                            $this->accepted[] = $input;
                        } elseif ($destination === self::REJECTED) {
                            // do nothing
                        } else {
                            $this->calculateInput($input, $destination);
                        }
                        return;
                    }
                    break;
                case self::SMALLER_THAN:
                    if ($input[$condition['variable']] < $condition['amount']) {
                        if ($destination === self::ACCEPTED) {
                            $this->accepted[] = $input;
                        } elseif ($destination === self::REJECTED) {
                            // do nothing
                        } else {
                            $this->calculateInput($input, $destination);
                        }
                        return;
                    }
                    break;
                default:
                    if ($destination === self::ACCEPTED) {
                        $this->accepted[] = $input;
                    } elseif ($destination === self::REJECTED) {
                        // do nothing
                    } else {
                        $this->calculateInput($input, $destination);
                    }
                    return;
            }
        }
    }

    public function runPart02(): int
    {
        $this->loadData();

        $group = [
            'x' => [
                'min' => 1,
                'max' => 4000,
            ],
            'm' => [
                'min' => 1,
                'max' => 4000,
            ],
            'a' => [
                'min' => 1,
                'max' => 4000,
            ],
            's' => [
                'min' => 1,
                'max' => 4000,
            ],
            'rule' => self::INPUT_RULE,
        ];

        $this->calculateGroups($group);

        $total = 0;

        foreach ($this->accepted as $item) {
            $total += $this->calculateGroup($item);
        }

        return $total;
    }

    private function calculateGroup(array $group): int
    {
        return ($group['x']['max'] - $group['x']['min'] + 1)
            * ($group['m']['max'] - $group['m']['min'] + 1)
            * ($group['a']['max'] - $group['a']['min'] + 1)
            * ($group['s']['max'] - $group['s']['min'] + 1);
    }

    private function calculateGroups(array $initGroup): void
    {
        $groups = [$initGroup];

        while ($group = array_shift($groups)) {
            $rule = $group['rule'];
            foreach ($this->rules[$rule] as $condition) {
                $operation = $condition['operation'] ?? null;
                $variable = $condition['variable'] ?? null;
                $destination = $condition['destination'];

                switch ($operation) {
                    case self::BIGGER_THAN:
                        $newGroup1 = $group;
                        $newGroup2 = $group;
                        $newGroup1[$variable]['min'] = $condition['amount'] + 1;
                        $newGroup2[$variable]['max'] = $condition['amount'];
                        break;
                    case self::SMALLER_THAN:
                        $newGroup1 = $group;
                        $newGroup2 = $group;
                        $newGroup1[$variable]['max'] = $condition['amount'] - 1;
                        $newGroup2[$variable]['min'] = $condition['amount'];
                        break;
                    default:
                        if ($destination === self::ACCEPTED) {
                            $this->accepted[] = $group;
                        } elseif ($destination === self::REJECTED) {
                            $this->rejected[] = $group;
                        } else {
                            $group['rule'] = $condition['destination'];
                            $groups[] = $group;
                        }
                        break 2;
                }

                $group = $newGroup2;

                if ($destination === self::ACCEPTED) {
                    $this->accepted[] = $newGroup1;
                } elseif ($destination === self::REJECTED) {
                    $this->rejected[] = $newGroup1;
                } else {
                    $newGroup1['rule'] = $condition['destination'];
                    $groups[] = $newGroup1;
                }
            }
        }
    }

    private function loadData(): void
    {
        $input = false;
        $i = 0;

        foreach ($this->data as $row => $data) {
            if (empty($data)) {
                $input = true;
                continue;
            }

            if ($input) {
                $data = str_replace(['{', '}'], '', $data);

                foreach (explode(',', $data) as $item) {
                    list($name, $value) = explode('=', $item);

                    $this->input[$i][$name] = $value;
                }
                $i++;
            } else {
                $data = str_replace('}', '', $data);
                list($rule, $conditions) = explode('{', $data);

                foreach (explode(',', $conditions) as $condition) {
                    if (str_contains($condition, self::BIGGER_THAN)) {
                        list($variable, $rest) = explode(self::BIGGER_THAN, $condition);
                        list($amount, $destination) = explode(':', $rest);
                        $this->rules[$rule][] = [
                            'variable' => $variable,
                            'operation' => self::BIGGER_THAN,
                            'amount' => $amount,
                            'destination' => $destination,
                        ];
                    } elseif (str_contains($condition,  self::SMALLER_THAN)) {
                        list($variable, $rest) = explode(self::SMALLER_THAN, $condition);
                        list($amount, $destination) = explode(':', $rest);
                        $this->rules[$rule][] = [
                            'variable' => $variable,
                            'operation' => self::SMALLER_THAN,
                            'amount' => $amount,
                            'destination' => $destination,
                        ];
                    } else {
                        $this->rules[$rule][] = [
                            'destination' => $condition
                        ];
                    }
                }
            }
        }
    }
}
