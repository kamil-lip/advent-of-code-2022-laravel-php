<?php

namespace App\Puzzles;

use Illuminate\Support\Facades\Storage;

class Puzzle6 implements PuzzleInterface
{

    protected function getData(string $file): array
    {
        $inputText = Storage::disk('local')->get("input/{$file}");
        return array_filter(explode("\n", $inputText));
    }

    public function solve_p1(string $inputFile): string
    {
        $lines = collect($this->getData($inputFile));

        return $lines->map(function($line){
            return $this->get_answer($line, 4);
        })->implode(' ');
    }

    protected function get_answer(string $line, $howManyDistinctChars): int
    {
        $chars = collect(str_split($line));

        $charsCount = $chars->count();
        for ($i = $howManyDistinctChars; $i < $charsCount; $i++) {
            $lastNChars = $chars->slice($i - $howManyDistinctChars, $howManyDistinctChars);
            if ($lastNChars->unique()->count() === $howManyDistinctChars) {
                return $i;
            }
        }
        return -1;
    }


    public function solve_p2(string $inputFile): string
    {
        $lines = collect($this->getData($inputFile));

        return $lines->map(function($line){
            return $this->get_answer($line, 14);
        })->implode(' ');
    }
}
