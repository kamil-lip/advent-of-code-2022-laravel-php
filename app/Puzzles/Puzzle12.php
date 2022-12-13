<?php

namespace App\Puzzles;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Puzzle12 implements PuzzleInterface
{

    protected function getData(string $file): Collection
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        $lines = array_filter(explode("\n", $input_text));
        return collect(array_map(function ($line) {
            return collect(str_split($line));
        }, $lines));
    }

    protected function findShortestPath(int $sy, int $sx, Collection &$area): int
    {
        $area[$sy][$sx] = 'a';

        $ySize = $area->count();
        $xSize = $area[0]->count();

        $stepMatrix = Collection::times($ySize, function ($number) use ($xSize) {
            return collect()->pad($xSize, PHP_INT_MAX);
        });

        $minSteps = PHP_INT_MAX;

        $toVisit = collect([[$sy, $sx]]);
        $stepMatrix[$sy][$sx] = 0;

        while ([$y, $x] = $toVisit->shift()) {
            $steps = $stepMatrix[$y][$x];
            $moveOptions = [[$y, $x - 1], [$y - 1, $x], [$y, $x + 1], [$y + 1, $x]];
            $validMoveOptions = array_filter($moveOptions, function (array $pos) use ($ySize, $xSize) {
                [$y, $x] = $pos;
                return $y >= 0 && $x >= 0 && $y < $ySize && $x < $xSize;
            });

            foreach ($validMoveOptions as [$newY, $newX]) {
                if ($area[$newY][$newX] == 'E' && chr(ord($area[$y][$x]) + 1) >= 'z' && $steps + 1 < $minSteps) {
                    $minSteps = $steps + 1;
                } elseif ($steps + 1 < $stepMatrix[$newY][$newX] && $area[$newY][$newX] <= chr(ord($area[$y][$x]) + 1)) {

                    $stepMatrix[$newY][$newX] = $steps + 1;
                    $toVisit->add([$newY, $newX]);
                }
            }
        }

        return $minSteps;
    }

    protected function findPosition(&$data, string $needle)
    {
        foreach ($data as $y => &$row) {
            foreach ($row as $x => &$char) {
                if ($char == $needle) {
                    return [$y, $x];
                }
            }
        }
    }

    protected function findSquaresWIthA(Collection &$area): Collection
    {
        $squares = collect();
        foreach ($area as $y => &$row) {
            foreach ($row as $x => &$char) {
                if ($char == 'a' || $char == 'S') {
                    $squares->push([$y, $x]);
                }
            }
        }
        return $squares;
    }

    public function solve_p1(string $inputFile): string
    {
        $area = $this->getData($inputFile);

        [$sy, $sx] = $this->findPosition($area, 'S');

        return $this->findShortestPath($sy, $sx, $area);
    }

    public function solve_p2(string $inputFile): string
    {
        $area = $this->getData($inputFile);

        $startingPoints = $this->findSquaresWIthA($area);

        return $startingPoints->map(function (array $startingPoint) use (&$area) {
            [$sy, $sx] = $startingPoint;
            return $this->findShortestPath($sy, $sx, $area);
        })->min();
    }
}
