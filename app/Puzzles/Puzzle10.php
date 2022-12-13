<?php

namespace App\Puzzles;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Puzzle10 implements PuzzleInterface
{

    protected function getData(string $file): array
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        return array_filter(explode("\n", $input_text));
    }

    public function solve_p1(string $inputFile): string
    {
        $data = $this->getData($inputFile);

        $c = 1;
        $x = 1;

        $sumX = 0;
        foreach ($data as $line) {
            $parts = explode(' ', $line);

            $cmd = $parts[0];
            $arg = $parts[1] ?? null;
            $arg = intval($arg);


            $cycles = ($cmd === 'addx') + 1;
            for ($y = 0; $y < $cycles; $y++) {
                if (($c + 20) % 40 == 0 && $c <= 220) {
                    $sumX += $c * $x;
                }
                if($y==1)
                {
                    $x += $arg;
                }
                $c++;
            }
        }

        return $sumX;
    }


    public function solve_p2(string $inputFile): string
    {
        $data = $this->getData($inputFile);

        $c = 1;
        $x = 1;

        $image = collect();
        $crtLine = collect();

        foreach ($data as $line) {
            $parts = explode(' ', $line);

            $cmd = $parts[0];
            $arg = $parts[1] ?? null;
            $arg = intval($arg);

            $cycles = ($cmd === 'addx') + 1;
            for ($y = 0; $y < $cycles; $y++) {
                $posX = $crtLine->count();

                $char = ($posX < $x-1 || $posX > $x+1) ? '.' : '#';
                $crtLine->add($char);

                if ($c % 40 == 0 && $c <= 240) {
                    $image->add($crtLine);
                    $crtLine = collect();
                }
                if($y==1)
                {
                    $x += $arg;
                }
                $c++;
            }
        }

        return "\n" . $image->map(function(Collection $line) {
            return $line->implode('');
        })->implode("\n");
    }
}
