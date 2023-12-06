<?php

namespace Shadowinek\Aoc2023;

class Puzzle05 extends AbstractPuzzle
{
    private const SEED_TO_SOIL = 'seed-to-soil';
    private const SOIL_TO_FERTILIZER = 'soil-to-fertilizer';
    private const FERTILIZER_TO_WATER = 'fertilizer-to-water';
    private const WATER_TO_LIGHT = 'water-to-light';
    private const LIGHT_TO_TEMPERATURE = 'light-to-temperature';
    private const TEMPERATURE_TO_HUMIDITY = 'temperature-to-humidity';
    private const HUMIDITY_TO_LOCATION = 'humidity-to-location';

    private array $seeds = [];

    private array $maps = [];

    private array $locations = [];

    public function runPart01(): int
    {
        $this->loadData(1);

        foreach ($this->seeds as $seed) {
            $this->processSeed($seed);
        }

        return min(array_keys($this->locations));
    }

    private function processSeed(int $seed): void
    {
        $soil = $this->findDestination($seed, self::SEED_TO_SOIL);
        $fertilizer = $this->findDestination($soil, self::SOIL_TO_FERTILIZER);
        $water = $this->findDestination($fertilizer, self::FERTILIZER_TO_WATER);
        $light = $this->findDestination($water, self::WATER_TO_LIGHT);
        $temperature = $this->findDestination($light, self::LIGHT_TO_TEMPERATURE);
        $humidity = $this->findDestination($temperature, self::TEMPERATURE_TO_HUMIDITY);
        $location = $this->findDestination($humidity, self::HUMIDITY_TO_LOCATION);

        $this->locations[$location] = $location;
    }

    private function findDestination(int $source, string $mapId): int
    {
        foreach ($this->maps[$mapId] as $map) {
            if ($source >= $map['minSource'] && $source <= $map['maxSource']) {
                $diff = $source - $map['minSource'];
                return $map['minDestination'] + $diff;
            }
        }

        return $source;
    }

    private function findDestinationRanges(array $ranges, string $mapId): array
    {
        $return = [];

        foreach ($ranges as $range) {
            $return = array_merge($return, $this->findDestinationRange($range, $mapId));
        }

        return $return;
    }

    private function findDestinationRange(array $range, string $mapId): array
    {
        $ranges = [];
        $toProcess = [$range];
        while ($processing = array_shift($toProcess)) {
            $start = $processing[0];
            $end = $processing[1];
            $processed = false;

            foreach ($this->maps[$mapId] as $map) {
                $shiftedStart = $start + $map['shift'];
                $shiftedEnd = $end + $map['shift'];

                if ($start >= $map['minSource'] && $start <= $map['maxSource']) {
                    $minEnd = min($shiftedEnd, $map['maxDestination']);
                    $ranges[] = [$shiftedStart, $minEnd];

                    if ($shiftedEnd > $minEnd) {
                        $toProcess[] = [$map['maxSource'] + 1, $end];
                    }

                    $processed = true;
                    break;
                } elseif ($end >= $map['minSource'] && $end <= $map['maxSource']) {
                    $maxStart = max($shiftedStart, $map['minDestination']);
                    $ranges[] = [$maxStart, $shiftedEnd];

                    if ($shiftedStart < $maxStart) {
                        $toProcess[] = [$start, $map['minSource'] - 1];
                    }

                    $processed = true;
                    break;
                } elseif ($map['minSource'] >= $start && $map['minSource'] <= $end) {
                    $ranges[] = [$map['minDestination'], min($shiftedEnd, $map['maxDestination'])];
                    $toProcess[] = [$start, $map['minSource'] - 1];
                    $processed = true;
                    break;
                }
            }

            if (!$processed) {
                $ranges[] = $processing;
            }
        }

        return $ranges;
    }

    private function processSeedRange(int $start, int $end): void
    {
        $seedRange[] = [$start, $end];
        $soil = $this->findDestinationRanges($seedRange, self::SEED_TO_SOIL);
        $fertilizer = $this->findDestinationRanges($soil, self::SOIL_TO_FERTILIZER);
        $water = $this->findDestinationRanges($fertilizer, self::FERTILIZER_TO_WATER);
        $light = $this->findDestinationRanges($water, self::WATER_TO_LIGHT);
        $temperature = $this->findDestinationRanges($light, self::LIGHT_TO_TEMPERATURE);
        $humidity = $this->findDestinationRanges($temperature, self::TEMPERATURE_TO_HUMIDITY);
        $this->locations[] = $this->findDestinationRanges($humidity, self::HUMIDITY_TO_LOCATION);
    }

    public function runPart02(): int
    {
        $this->loadData(2);

        foreach ($this->seeds as $seedRange) {
            $this->processSeedRange($seedRange[0], $seedRange[0] + $seedRange[1] - 1);
        }

        $min = [];

        foreach ($this->locations as $location) {
            foreach ($location as $seed) {
                $min[] = $seed[0];
            }
        }

        return min($min);
    }

    private function loadData(int $part): void
    {
        $currentMap = '';

        foreach ($this->data as $data) {
            if (str_starts_with($data, 'seeds: ')) {
                $numbers = $this->parseNumbers(substr($data, 7));

                if ($part === 1) {
                	$this->parseSeedsForPart1($numbers);
                } else {
                    $this->parseSeedsForPart2($numbers);
                }

            } elseif (str_starts_with($data, self::SEED_TO_SOIL)) {
                $currentMap = self::SEED_TO_SOIL;
            } elseif (str_starts_with($data, self::SOIL_TO_FERTILIZER)) {
                $currentMap = self::SOIL_TO_FERTILIZER;
            } elseif (str_starts_with($data, self::FERTILIZER_TO_WATER)) {
                $currentMap = self::FERTILIZER_TO_WATER;
            } elseif (str_starts_with($data, self::WATER_TO_LIGHT)) {
                $currentMap = self::WATER_TO_LIGHT;
            } elseif (str_starts_with($data, self::LIGHT_TO_TEMPERATURE)) {
                $currentMap = self::LIGHT_TO_TEMPERATURE;
            } elseif (str_starts_with($data, self::TEMPERATURE_TO_HUMIDITY)) {
                $currentMap = self::TEMPERATURE_TO_HUMIDITY;
            } elseif (str_starts_with($data, self::HUMIDITY_TO_LOCATION)) {
                $currentMap = self::HUMIDITY_TO_LOCATION;
            } elseif (empty($data)) {
                continue;
            } else {
                $numbers = $this->parseNumbers($data);

                $this->maps[$currentMap][(int) $numbers[1]] = [
                    'minDestination' => (int) $numbers[0],
                    'maxDestination' => (int) ($numbers[0] + $numbers[2] - 1),
                    'minSource' => (int) $numbers[1],
                    'maxSource' => (int) ($numbers[1] + $numbers[2] - 1),
                    'range' => (int) $numbers[2],
                    'shift' => (int) ($numbers[0] - $numbers[1]),
                ];
            }
        }
    }

    private function parseSeedsForPart1(array $numbers): void
    {
        foreach ($numbers as $number) {
            $this->seeds[] = (int) $number;
        }
    }

    private function parseSeedsForPart2(array $numbers): void
    {
        $i = 0;
        $j = 0;
        foreach ($numbers as $number) {
            $i++;
            $this->seeds[$j][] = $number;

            if ($i === 2) {
                $i = 0;
                $j++;
            }
        }
    }
}
