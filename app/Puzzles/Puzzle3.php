<?php

namespace App\Puzzles;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Puzzle3 implements PuzzleInterface
{
    public function get_data(string $file): Collection
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        $lines = collect(explode("\n", $input_text));
        $data = collect();
        foreach ($lines as $line) {
            $line_length = strlen($line);
            if ($line_length > 1) {
                $s1 = substr($line, 0, $line_length / 2);
                $s2 = substr($line, $line_length / 2);
                $data->add([$s1, $s2]);
            }
        }
        return $data;
    }


    public function solve_p1(string $inputFile): string
    {
        $data = $this->get_data($inputFile);

        $priority_total = 0;

        foreach ($data as [$s1, $s2]) {
            $shared_letter = null;
            foreach (str_split($s1) as $letter) {
                if (strpos($s2, $letter) !== false) {
                    $shared_letter = $letter;
                }
            }

            $priority = $this->get_priority($shared_letter);

            $priority_total += $priority;
        }


        return $priority_total;
    }

    protected function get_priority(string $letter): int
    {
        if ($letter >= 'a' && $letter <= 'z') {
            return ord($letter) - 96;
        }
        if ($letter >= 'A' && $letter <= 'Z') {
            return ord($letter) - 64 + 26;
        }
    }

    public function get_data2(string $file): Collection
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        $lines = collect(explode("\n", $input_text));

        return $lines->filter();
    }

    public function solve_p2(string $inputFile): string
    {
        $data = $this->get_data2($inputFile);

        $priority_total = 0;

        $line_count = count($data);

        for ($i = 0; $i < $line_count; $i += 3) {
            $shared_letter = null;
            foreach (str_split($data[$i]) as $letter) {
                if (strpos($data[$i + 1], $letter) !== false && strpos($data[$i + 2], $letter) !== false) {
                    $shared_letter = $letter;
                }
            }

            $priority = $this->get_priority($shared_letter);

            $priority_total += $priority;
        }

        return $priority_total;
    }
}
