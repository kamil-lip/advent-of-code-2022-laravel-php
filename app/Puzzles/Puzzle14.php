<?php

namespace App\Puzzles;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Puzzle14 implements PuzzleInterface
{
    public function getData(string $file)
    {
        $inputText = Storage::disk('local')->get("input/{$file}");
        $lines = collect(array_filter(explode("\n", $inputText)));
        $cave = collect();

        foreach ($lines as $line) {
            $points = collect(explode(' -> ', $line));
            $path = $points->map(function ($point) {
                return array_map('intval', explode(',', $point));
            });
            $this->drawPath($cave, $path);
        }

        return $cave;
    }

    protected function drawPath(Collection $cave, Collection $path)
    {
        $pointsCount = count($path);

        for ($i = 0; $i < $pointsCount - 1; $i++) {
            [$x1, $y1] = $path[$i];
            [$x2, $y2] = $path[$i+1];

            $xFrom = min($x1, $x2);
            $xTo = max($x1, $x2);

            $yFrom = min($y1, $y2);
            $yTo = max($y1, $y2);

            if (!$cave->has($yFrom)) {
                $cave[$yFrom] = collect();
            }

            if($yFrom == $yTo)
            {
                $row = $cave[$y1];

                for ($x = $xFrom; $x <= $xTo; $x++) {
                    if (!$row->has($x)) {
                        $row->put($x, '#');
                    }
                }
            }

            if($xFrom == $xTo)
            {
                for ($y = $yFrom; $y <= $yTo; $y++) {
                    if (!$cave->has($y)) {
                        $cave->put($y, collect());
                    }
                    $cave[$y]->put($xFrom, '#');
                }
            }

        }
    }

    public function solve_p1(string $inputFile): string
    {
        $cave = $this->getData($inputFile);

        $bottomLevel = $cave->keys()->max();

        $unitNo = 0;

        do
        {
            $x = 500;
            $y = 0;

            $unitNo++;

            while(true)
            {
                if($y>$bottomLevel)
                {
                    return $unitNo-1;
                }
                if(!isset($cave[$y+1][$x]))
                {
                    $y++;
                    continue;
                }
                if(!isset($cave[$y+1][$x-1]))
                {
                    $y++;
                    $x--;
                    continue;
                }
                if(!isset($cave[$y+1][$x+1]))
                {
                    $y++;
                    $x++;
                    continue;
                }
                if(!$cave->has($y))
                {
                    $cave->put($y, collect());
                }
                $cave[$y]->put($x, 'o');
                break;
            }

        } while(1);
    }

    public function solve_p2(string $inputFile): string
    {
        $cave = $this->getData($inputFile);

        $bottomLevel = $cave->keys()->max() + 2;

        $unitNo = 0;

        do
        {
            $x = 500;
            $y = 0;

            $unitNo++;

            while(true)
            {
                if(!isset($cave[$y+1][$x]) && $y+1<$bottomLevel)
                {
                    $y++;
                    continue;
                }
                if(!isset($cave[$y+1][$x-1]) && $y+1<$bottomLevel)
                {
                    $y++;
                    $x--;
                    continue;
                }
                if(!isset($cave[$y+1][$x+1]) && $y+1<$bottomLevel)
                {
                    $y++;
                    $x++;
                    continue;
                }
                if(!$cave->has($y))
                {
                    $cave->put($y, collect());
                }
                $cave[$y]->put($x, 'o');
                if($y == 0 && $x == 500)
                {
                    return $unitNo;
                }
                break;
            }

        } while(true);
    }
}