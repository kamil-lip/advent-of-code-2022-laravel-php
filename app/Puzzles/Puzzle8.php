<?php

namespace App\Puzzles;

use Illuminate\Support\Facades\Storage;

class Puzzle8 implements PuzzleInterface
{

    public function getData(string $file): array
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        $lines = array_filter(explode("\n", $input_text));

        return array_map('str_split', $lines);
    }


    public function solve_p1(string $inputFile): string
    {
        $data = $this->getData($inputFile);

        $visible_trees = 0;

        $row_count = count($data);
        $col_count = count($data[0]);

        for($y=0; $y<$row_count; $y++)
        {
            $row = $data[$y];

            for($x=0; $x<$col_count; $x++)
            {
                if($x==0 || $y==0 || $x==$col_count-1 || $y==$row_count-1)
                {
                    $visible_trees++;
                    continue;
                }

                $visible = true;
                for($i=0; $i<$x; $i++) {
                    $visible = $visible && ($row[$i] < $row[$x]);
                }
                if($visible) {
                    $visible_trees++;
                    continue;
                }

                $visible = true;
                for($i=0; $i<$y; $i++) {
                    $visible = $visible && ($data[$i][$x] < $row[$x]);
                }
                if($visible) {
                    $visible_trees++;
                    continue;
                }

                $visible = true;
                for($i=$col_count-1; $i>$x; $i--) {
                    $visible = $visible && ($row[$i] < $row[$x]);
                }
                if($visible) {
                    $visible_trees++;
                    continue;
                }

                $visible = true;
                for($i=$row_count-1; $i>$y; $i--) {
                    $visible = $visible && ($data[$i][$x] < $row[$x]);
                }
                if($visible) {
                    $visible_trees++;
                    continue;
                }
            }
        }

        return $visible_trees;
    }


    public function solve_p2(string $inputFile): string
    {
        $data = $this->getData($inputFile);

        $max_score = 0;

        $row_count = count($data);
        $col_count = count($data[0]);

        for($y=0; $y<$row_count; $y++)
        {
            $row = $data[$y];

            for($x=0; $x<$col_count; $x++)
            {
                if($x==0 || $y==0 || $x==$col_count-1 || $y==$row_count-1)
                {
                    continue;
                }

                $visible_trees = 0;
                for($i=$x-1; $i>=0; $i--) {
                    $visible_trees++;
                    if($row[$i] >= $row[$x]) {
                        break;
                    }
                }
                $score = $visible_trees;

                $visible_trees = 0;
                for($i=$x+1; $i<$col_count; $i++) {
                    $visible_trees++;
                    if($row[$i] >= $row[$x]) {
                        break;
                    }
                }
                $score *= $visible_trees;

                $visible_trees = 0;
                for($i=$y-1; $i>=0; $i--) {
                    $visible_trees++;
                    if($data[$i][$x] >= $data[$y][$x]) {
                        break;
                    }
                }
                $score *= $visible_trees;

                $visible_trees = 0;
                for($i=$y+1; $i<$row_count; $i++) {
                    $visible_trees++;
                    if($data[$i][$x] >= $data[$y][$x]) {
                        break;
                    }
                }
                $score *= $visible_trees;

                if($score > $max_score) {
                    $max_score = $score;
                }
            }
        }


        return $max_score;
    }
}
