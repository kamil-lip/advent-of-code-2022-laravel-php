<?php

namespace App\Puzzles;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Puzzle13
{
    protected function getData(string $file): Collection
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        $lines = collect(array_filter(explode("\n", $input_text)));

        return $lines->map(function ($line) {
            eval("\$packet = $line;");
            return $packet;
        })->values();

    }

    protected function compare(array|int $p1, array|int $p2): int
    {
        if (is_int($p1) && is_int($p2)) {
            return $p1 - $p2;
        }

        $p1 = (array) $p1;
        $p2 = (array) $p2;

        $p2Count = count($p2);

        foreach ($p1 as $i => &$p1Item) {
            if ($i >= $p2Count) {
                return 1;
            }
            $p2Item = &$p2[$i];
            if (($res = $this->compare($p1Item, $p2Item)) != 0) {
                return $res;
            }
        }

        return count($p1) - $p2Count;
    }

    public function solve_p1(string $inputFile): string
    {
        $packets = $this->getData($inputFile);

        $packetsWithRightOrder = collect();

        $packetCount = count($packets);

        for ($i = 0; $i < $packetCount; $i += 2) {
            $packet1 = $packets[$i];
            $packet2 = $packets[$i + 1];

            if ($this->compare($packet1, $packet2) < 0) {
                $packetsWithRightOrder->add(($i / 2) + 1);
            }
        }

        return $packetsWithRightOrder->sum();
    }

    public function solve_p2(string $inputFile): string
    {
        $packets = $this->getData($inputFile);
        $packets->add([[2]]);
        $packets->add([[6]]);

        $sorted = $packets->sortBy([
            fn (array $a, array $b) => $this->compare($a, $b)
        ]);

        $ind1 = $sorted->search(function ($item, $key) {
            return $item === [[2]];
        }) + 1;

        $ind2 = $sorted->search(function ($item, $key) {
            return $item === [[6]];
        }) + 1;

        return $ind1 * $ind2;
    }

}
