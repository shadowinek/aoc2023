<?php

namespace Shadowinek\Aoc2023;

class Puzzle22 extends AbstractPuzzle
{
    private array $orders = [];
    private int $total = 0;
    private int $skipped = 0;

    public function runPart01(): int
    {
        $this->loadData();

        echo $this->total . PHP_EOL;
        echo $this->skipped . PHP_EOL;

        $fp = fopen('file.csv', 'w');
        foreach ($this->orders as $fields) {
            fputcsv($fp, $fields);
        }

        return $this->calculate();
    }

    private function calculate(): int
    {
        return 0;
    }

    public function runPart02(): int
    {
        return 0;
    }

    private function loadData(): void
    {
        foreach ($this->data as $data) {
            // --startDate=2021-03-01 --endDate=2021-04-30 - 500 (61)
//            if (str_starts_with($data, '--startDate=')) {
//                preg_match('/--startDate=(\d{4}-\d{2}-\d{2}) --endDate=(\d{4}-\d{2}-\d{2}) - (\d+) \((\d+)\)/', $data, $matches);
//
//                list($full, $startDate, $endDate, $total, $skipped) = $matches;
//                $this->total += (int) $total;
//                $this->skipped += (int) $skipped;
//            }

            // [WARNING] Error while recalculating order 2387: Product is not an instance of PurchasableInterface
            if (str_starts_with($data, '[WARNING] Error while processing order ')) {
                preg_match('/(\d+): (.*)/', $data, $matches2);
                list($full, $orderId, $message) = $matches2;

                $this->orders[(int) $orderId] = [
                    'orderId' => (int) $orderId,
                    'message' => $message,
//                    'startDate' => $startDate ?? null,
//                    'endDate' => $endDate ?? null,
                ];
            }
        }
    }
}
