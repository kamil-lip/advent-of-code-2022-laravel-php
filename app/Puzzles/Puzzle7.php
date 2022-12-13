<?php

namespace App\Puzzles;

use Illuminate\Support\Facades\Storage;

class Puzzle7 implements PuzzleInterface
{
    protected const DISK_SPACE = 70000000;
    protected const SPACE_REQUIRED = 30000000;

    protected function getData(string $file): array
    {
        $input_text = Storage::disk('local')->get("input/{$file}");
        return array_filter(explode("\n", $input_text));
    }

    public function solve_p1(string $inputFile): string
    {
        $data = $this->getData($inputFile);
        $tree = &$this->buildDirTree($data);
        $this->calculateNodeSize($tree);

        $dirs = collect([$tree]);

        $totalSize = 0;

        while($dir = $dirs->shift())
        {
            foreach ($dir as $name => &$entry)
            {
                if(is_array($entry) && $name != '..')
                {
                    $dirs->add($entry);
                }
            }
            if($dir['_size'] <= 100000)
            {
                $totalSize += $dir['_size'];
            }
        }

        return $totalSize;
    }

    protected function calculateNodeSize(array &$node): int
    {
        $size = 0;
        foreach ($node as $name => &$item)
        {
            if(is_int($item) && $name != '_size') {
                $size += $item;
            } elseif($name != '..') {
                $size += $this->calculateNodeSize($item);
            }
        }
        $node['_size'] = $size;
        return $node['_size'];
    }

    protected function &buildDirTree(array $lines)
    {
        $root = array();

        $currentDir = null;

        foreach ($lines as $line) {
            if (str_starts_with($line, '$ ')) {
                $parts = explode(' ', $line);
                $cmd = $parts[1];
                $arg = $parts[2] ?? null;

                switch ($cmd) {
                    case 'cd':
                        if ($arg == '/') {
                            $currentDir = &$root;
                        } else {
                            $currentDir = &$currentDir[$arg];
                        }
                        break;

                }
            } else {
                [$part1, $name] = explode(' ', $line);
                if ($part1 == 'dir') {
                    $currentDir[$name] = [
                        '..' => &$currentDir
                    ];
                } else {
                    $currentDir[$name] = intval($part1);
                }
            }
        }
        return $root;
    }

    public function solve_p2(string $inputFile): string
    {
        $data = $this->getData($inputFile);
        $tree = &$this->buildDirTree($data);
        $this->calculateNodeSize($tree);

        $freeSpace = $this->getFreeSpace($tree);
        $spaceNeeded = self::SPACE_REQUIRED - $freeSpace;

        $dirs = collect([$tree]);

        $candidates = collect();

        while($dir = $dirs->shift())
        {
            foreach ($dir as $name => &$entry)
            {
                if(is_array($entry) && $name != '..')
                {
                    $dirs->add($entry);
                }
            }
            if($dir['_size'] >= $spaceNeeded)
            {
                $candidates->add($dir);
            }
        }

        $candidatesBySizeDesc = $candidates->sortBy(function($candidate) {
            return $candidate['_size'];
        });

        return $candidatesBySizeDesc->first()['_size'];
    }

    protected function getFreeSpace(array &$root)
    {
        return self::DISK_SPACE - $root['_size'];
    }
}
