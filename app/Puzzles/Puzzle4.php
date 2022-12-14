<?php

namespace App\Puzzles;

use Illuminate\Support\Facades\Storage;

class Puzzle4 implements PuzzleInterface
{
    public function get_data(string $file): array
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        $lines = explode("\n", $input_text);

        return array_filter($lines, function ($line) {
            return strlen($line) > 1;
        });
    }


    public function solve_p1(string $inputFile): string
    {
        $data = $this->get_data($inputFile);

        $res = 0;
        foreach ($data as $line_data) {
            $sections = explode(',', $line_data);
            [$x1, $x2] = array_map('intval', explode('-', $sections[0]));
            [$y1, $y2] = array_map('intval', explode('-', $sections[1]));

            if (($x1 <= $y1 && $x2 >= $y2) || ($y1 <= $x1 && $y2 >= $x2)) {
                $res++;
            }
        }
        return $res;
    }


    public function solve_p2(string $inputFile): string
    {
        $data = $this->get_data($inputFile);

        $res = 0;
        foreach ($data as $line_data) {
            $sections = explode(',', $line_data);
            [$x1, $x2] = array_map('intval', explode('-', $sections[0]));
            [$y1, $y2] = array_map('intval', explode('-', $sections[1]));

            if (!($x1 > $y2 || $x2 < $y1)) {
                $res++;
            }
        }
        return $res;
    }
}
