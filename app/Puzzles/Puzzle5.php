<?php

namespace App\Puzzles;

use Illuminate\Support\Facades\Storage;

class Puzzle5 implements PuzzleInterface
{

    protected function getData(string $file): array
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        $lines = explode("\n", $input_text);

        $stacks = collect([null]);

        foreach ($lines as $i => $line) {
            if (!$line) {
                $moves = collect(array_splice($lines, $i + 1))->filter();
                break;
            }

            preg_match_all('/\ \d\ ?/', $line, $matches);

            if ($matches[0]) { // stack numbers
                $stacks = $stacks->map(function ($stack) {
                    return $stack ? $stack->reverse() : $stack;
                });
                continue;
            }

            preg_match_all('/[\[\ ](.)[\]\ ]\ ?/', $line, $matches);

            $crates = $matches[1];
            $crates_count = count($crates);

            for ($i = 0; $i < $crates_count; $i++) {
                $crate = $crates[$i];

                if ($i + 1 >= $stacks->count()) {
                    $stacks->add(collect());
                }

                if ($crate != ' ') {
                    $stacks[$i + 1]->add($crate);
                }
            }
        }

        return compact('stacks', 'moves');
    }

    public function solve_p1(string $inputFile): string
    {
        $data = $this->getData($inputFile);

        extract($data);

        foreach ($moves as $move) {
            preg_match('/move (\d+) from (\d+) to (\d+)/', $move, $matches);

            [, $howMany, $from, $to] = $matches;

            $poppedCrates = $stacks[(int) $from]->pop($howMany);

            $destStack = $stacks[(int) $to];

            foreach ($poppedCrates as $crate) {
                $destStack->push($crate);
            }
        }

        return $stacks->splice(1)->map(function ($stack) {
            return $stack->last();
        })->implode('');
    }


    public function solve_p2(string $inputFile): string
    {
        $data = $this->getData($inputFile);

        extract($data);

        foreach ($moves as $move) {
            preg_match('/move (\d+) from (\d+) to (\d+)/', $move, $matches);

            [, $howMany, $from, $to] = $matches;

            $poppedCrates = $stacks[(int) $from]->pop($howMany)->reverse();

            $destStack = $stacks[(int) $to];

            foreach ($poppedCrates as $crate) {
                $destStack->push($crate);
            }
        }

        return $stacks->splice(1)->map(function ($stack) {
            return $stack->last();
        })->implode('');
    }
}
