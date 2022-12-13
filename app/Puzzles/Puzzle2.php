<?php

namespace App\Puzzles;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Puzzle2 implements PuzzleInterface
{
    protected const SCORES_FOR_SHAPES = [
        self::ME_ROCK => 1,
        self::ME_PAPER => 2,
        self::ME_SCISSORS => 3
    ];

    protected const OPP_ROCK = 'A';
    protected const OPP_PAPER = 'B';
    protected const OPP_SCISSORS = 'C';

    protected const ME_ROCK = 'X';
    protected const ME_PAPER = 'Y';
    protected const ME_SCISSORS = 'Z';

    protected const LOSE = 'X';
    protected const DRAW = 'Y';
    protected const WIN = 'Z';

    public function get_data(string $file): Collection
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        $lines = explode("\n", $input_text);

        $data = collect();

        foreach ($lines as $line) {
            $shapes = collect(explode(' ', trim($line)));
            if ($shapes->count() == 2) {
                $data->add($shapes);
            }
        }

        return $data;
    }

    public function get_score_for_shape(string $shape): int
    {
        return self::SCORES_FOR_SHAPES[$shape];
    }

    public function get_score_for_outcome(Collection $shapes): int
    {
        $shapes_array = $shapes->toArray();

        if (in_array($shapes_array, [
                [self::OPP_ROCK, self::ME_ROCK],
                [self::OPP_PAPER, self::ME_PAPER],
                [self::OPP_SCISSORS, self::ME_SCISSORS],
            ]
        )) {
            return 3;
        }

        if ($shapes_array == [self::OPP_ROCK, self::ME_PAPER]
            || $shapes_array == [self::OPP_PAPER, self::ME_SCISSORS]
            || $shapes_array == [self::OPP_SCISSORS, self::ME_ROCK]
        ) {
            return 6;
        }
        return 0;
    }

    public function solve_p1(string $inputFile): string
    {
        $data = $this->get_data($inputFile);
        return $this->get_score($data);
    }

    protected function get_score(Collection $data): int
    {
        $score = 0;
        foreach ($data as $shapes) {
            $round_score = $this->get_score_for_shape($shapes->get(1)) + $this->get_score_for_outcome($shapes);
            $score += $round_score;
        }
        return $score;
    }

    protected function get_shape_needed(array $opp_shape_and_result): string
    {
        switch ($opp_shape_and_result) {
            case [self::OPP_ROCK, self::LOSE]:
            case [self::OPP_SCISSORS, self::DRAW]:
            case [self::OPP_PAPER, self::WIN]:
                return self::ME_SCISSORS;
            case [self::OPP_SCISSORS, self::LOSE]:
            case [self::OPP_PAPER, self::DRAW]:
            case [self::OPP_ROCK, self::WIN]:
                return self::ME_PAPER;
            default:
                return self::ME_ROCK;
        }
    }

    public function solve_p2(string $inputFile): string
    {
        $data = $this->get_data($inputFile);

        foreach ($data as &$opp_shape_and_result) {
            $my_shape = $this->get_shape_needed($opp_shape_and_result->toArray());
            $opp_shape_and_result->put(1, $my_shape);
        }

        return $this->get_score($data);

    }
}
