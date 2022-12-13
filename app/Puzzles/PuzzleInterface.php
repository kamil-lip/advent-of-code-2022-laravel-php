<?php

namespace App\Puzzles;

interface PuzzleInterface
{
    public function solve_p1(string $inputFile): string;
    public function solve_p2(string $inputFile): string;
}