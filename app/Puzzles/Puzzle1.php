<?php

namespace App\Puzzles;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Puzzle1 implements PuzzleInterface
{
    protected function get_data(string $file): Collection
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        $lines = explode("\n", $input_text);

        $result = collect();

        $elf_calories = collect();
        foreach ($lines as $line) {
            if (is_numeric($line)) {
                $elf_calories->add(intval($line));
            } else {
                if (!$elf_calories->isEmpty()) {
                    $result->add($elf_calories);
                }
                $elf_calories = collect();
            }
        }

        return $result;
    }

    public function solve_p1(string $inputFile): string
    {
        $data = $this->get_data($inputFile);

        $max = 0;

        foreach ($data as $elf_calories) {
            $calories_sum = $elf_calories->sum();
            if ($calories_sum > $max) {
                $max = $calories_sum;
            }
        }

        return $max;
    }

    public function solve_p2(string $inputFile): string
    {
        $data = $this->get_data($inputFile);

        $sums = collect();

        foreach ($data as $elf_calories) {
            $sums->add($elf_calories->sum());
        }

        $sorted_sums = $sums->sortDesc();

        $top3 = $sorted_sums->shift(3);

        return $top3->sum();
    }
}
