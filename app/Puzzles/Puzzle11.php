<?php

namespace App\Puzzles;

use App\Puzzles\Puzzle11\Monkey;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Puzzle11 implements PuzzleInterface
{

    protected function getData(string $file, $useDivProduct = false): Collection
    {
        $inputText = Storage::disk('local')->get("input/{$file}");
        preg_match_all('/Monkey (\d+):
  Starting items: ((?:\d+(?:, )?)*)
  Operation: ([^\n]+)
  Test: divisible by (\d+)
    If true: throw to monkey (\d+)
    If false: throw to monkey (\d+)/sm', $inputText, $matches);

       [$texts, $monkey_numbers, $items, $operations, $divBy, $ifTrue, $ifFalse] = $matches;

       $monkeys = collect();

       if($useDivProduct)
       {
           Monkey::$divProduct = array_product($divBy);
       }

       foreach ($monkey_numbers as $i => $monkey_no)
       {
           $startingItems = collect(explode(', ', $items[$i]));
           $operation = $operations[$i];
           $monkeys->add(new Monkey($startingItems, $operation, $divBy[$i], $ifTrue[$i], $ifFalse[$i]));
       }

       return $monkeys;
    }

    public function solve_p1(string $inputFile): string
    {
        $monkeys = $this->getData($inputFile);

        return (string) $this->play($monkeys, 20);
    }

    protected function play($monkeys, int $rounds, int $divWorryLevelBy = 3): int
    {
        for($i=0; $i<$rounds; $i++)
        {
            foreach ($monkeys as $monkey)
            {
                while ($monkey->hasMoreItems())
                {
                    $playResult = $monkey->play($divWorryLevelBy);
                    if(!$playResult) break;
                    extract($playResult);
                    $monkeys[$throwTo]->catchItem($item);
                }
            }
        }

        $monkeysByInspections = $monkeys->sortByDesc(function(Monkey $monkey) {
            return $monkey->getInspectionCount();
        });

        $mostActiveMonkeys =  $monkeysByInspections->shift(2);

        return $mostActiveMonkeys[0]->getInspectionCount() * $mostActiveMonkeys[1]->getInspectionCount();
    }


    public function solve_p2(string $inputFile): string
    {
        $monkeys = $this->getData($inputFile, true);

        return (string) $this->play($monkeys, 10000, 1);
    }
}
