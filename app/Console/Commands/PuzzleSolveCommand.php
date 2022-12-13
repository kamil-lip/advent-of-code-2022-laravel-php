<?php

namespace App\Console\Commands;

use App\Puzzles\Puzzle2;
use Illuminate\Console\Command;

class PuzzleSolveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'puzzle:solve {day : Day (1 to 24)} {--test : Use test data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solves the puzzle for a specific day.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $day = $this->argument('day');

        $puzzle = app("App\Puzzles\Puzzle{$day}");

        $input_file = $this->option('test') ? "test{$day}.txt" : "puzzle{$day}.txt";

        $this->info('Part 1 result: ' . $puzzle->solve_p1($input_file));
        $this->info('Part 2 result: ' . $puzzle->solve_p2($input_file));

        return 0;
    }
}
