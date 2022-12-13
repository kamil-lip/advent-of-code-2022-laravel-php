<?php

namespace App\Puzzles\Puzzle11;

use Illuminate\Support\Collection;

class Monkey
{
    public static ?int $divProduct = null;

    protected $items;
    protected $operation;
    protected $divBy;
    protected $throwToIfTrue;
    protected $throwToIfFalse;
    protected $inspectionCount = 0;

    public function __construct(Collection $starting_items, string $operation, int $divBy, int $throwToIfTrue, int $throwToIfFalse)
    {
        $this->items = $starting_items;
        $this->operation = $operation;
        $this->divBy = $divBy;
        $this->throwToIfTrue = $throwToIfTrue;
        $this->throwToIfFalse = $throwToIfFalse;
    }

    public function catchItem(int $itemWorryLevel)
    {
        $this->items->add($itemWorryLevel);
    }

    public function hasMoreItems(): bool
    {
        return $this->items->isNotEmpty();
    }

    public function play(int $divWorryLevelBy = 3): ?array
    {
        $item = $this->items->shift();
        $newWorryLevel = (int) floor($this->operation($item) / $divWorryLevelBy);

        if(self::$divProduct)
        {
            $newWorryLevel = $newWorryLevel % self::$divProduct;
        }

        $this->inspectionCount++;

        return [
            'item' => $newWorryLevel,
            'throwTo' => $newWorryLevel % $this->divBy == 0 ? $this->throwToIfTrue : $this->throwToIfFalse
        ];
    }

    protected function operation(int $item): int
    {
        [,,$arg1, $op, $arg2] = explode(' ', $this->operation);

        if ($arg1 == 'old') {
            $arg1 = $item;
        }

        if ($arg2 == 'old') {
            $arg2 = $item;
        }

        return match ($op) {
            '*' => $arg1 * $arg2,
            '+' => $arg1 + $arg2,
            '/' => $arg1 / $arg2,
            '-' => $arg1 - $arg2,
        };
    }

    public function getInspectionCount(): int
    {
        return $this->inspectionCount;
    }
}
