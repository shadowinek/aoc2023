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

    private const INPUT_RULE = 'in';
    public function runPart01(): int
    {
        $this->loadData();

//        print_r($this->rules);
//        print_r($this->input);

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

        return 0;
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
