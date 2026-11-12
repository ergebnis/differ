<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/differ
 */

namespace Ergebnis\Differ;

/**
 * Produces the same unified diff as sebastian/diff with its StrictUnifiedDiffOutputBuilder when both strings end with a new line, using the linear-space variant of the Myers diff algorithm that sebastian/diff uses since version 9.0.0.
 *
 * @see https://github.com/sebastianbergmann/diff/blob/9.0.1/src/MyersDiff.php
 * @see https://github.com/sebastianbergmann/diff/blob/9.0.1/src/Output/StrictUnifiedDiffOutputBuilder.php
 */
final class MyersDiffer implements Differ
{
    private const CONTEXT_LINES = 3;
    private const OPERATION_ADDED = '+';
    private const OPERATION_REMOVED = '-';
    private const OPERATION_UNCHANGED = ' ';

    public function diff(
        From $from,
        To $to
    ): Diff {
        if ($from->text()->toString() === $to->text()->toString()) {
            return Diff::fromString('');
        }

        $operations = self::operations(
            self::lines($from->text()->toString()),
            self::lines($to->text()->toString()),
        );

        $diff = \sprintf(
            "--- %s\n+++ %s\n",
            $from->label()->toString(),
            $to->label()->toString(),
        );

        $fromLinesBefore = 0;
        $toLinesBefore = 0;
        $index = 0;

        foreach (self::hunks($operations) as [$start, $end]) {
            while ($index < $start) {
                if (self::OPERATION_ADDED !== $operations[$index][0]) {
                    ++$fromLinesBefore;
                }

                if (self::OPERATION_REMOVED !== $operations[$index][0]) {
                    ++$toLinesBefore;
                }

                ++$index;
            }

            $diff .= self::hunk(
                $operations,
                $start,
                $end,
                $fromLinesBefore,
                $toLinesBefore,
            );
        }

        return Diff::fromString($diff);
    }

    /**
     * @return list<string>
     */
    private static function lines(string $string): array
    {
        if ('' === $string) {
            return [];
        }

        $parts = \explode(
            "\n",
            $string,
        );

        $last = \array_pop($parts);

        $lines = [];

        foreach ($parts as $part) {
            $lines[] = $part . "\n";
        }

        if ('' !== $last) {
            $lines[] = $last;
        }

        return $lines;
    }

    /**
     * Computes the edit script with the linear-space variant of the Myers diff algorithm, then lists removed lines before added lines within each run of changes.
     *
     * @see https://doi.org/10.1007/BF01840446
     * @see https://github.com/sebastianbergmann/diff/blob/9.0.1/src/MyersDiff.php
     *
     * @param list<string> $from
     * @param list<string> $to
     *
     * @return list<array{0: string, 1: string}>
     */
    private static function operations(
        array $from,
        array $to
    ): array {
        $operations = self::diffRange(
            $from,
            0,
            \count($from),
            $to,
            0,
            \count($to),
        );

        $normalized = [];
        $added = [];

        foreach ($operations as $operation) {
            if (self::OPERATION_ADDED === $operation[0]) {
                $added[] = $operation;

                continue;
            }

            if (self::OPERATION_REMOVED === $operation[0]) {
                $normalized[] = $operation;

                continue;
            }

            foreach ($added as $addedOperation) {
                $normalized[] = $addedOperation;
            }

            $added = [];

            $normalized[] = $operation;
        }

        foreach ($added as $addedOperation) {
            $normalized[] = $addedOperation;
        }

        return $normalized;
    }

    /**
     * @param list<string> $from
     * @param list<string> $to
     *
     * @return list<array{0: string, 1: string}>
     */
    private static function diffRange(
        array $from,
        int $fromStart,
        int $fromEnd,
        array $to,
        int $toStart,
        int $toEnd
    ): array {
        $operations = [];

        while (
            $fromStart < $fromEnd
            && $toStart < $toEnd
            && $from[$fromStart] === $to[$toStart]
        ) {
            $operations[] = [
                self::OPERATION_UNCHANGED,
                $from[$fromStart],
            ];

            ++$fromStart;
            ++$toStart;
        }

        $suffix = [];

        while (
            $fromStart < $fromEnd
            && $toStart < $toEnd
            && $from[$fromEnd - 1] === $to[$toEnd - 1]
        ) {
            --$fromEnd;
            --$toEnd;

            $suffix[] = [
                self::OPERATION_UNCHANGED,
                $from[$fromEnd],
            ];
        }

        if ($fromStart === $fromEnd) {
            for ($index = $toStart; $index < $toEnd; ++$index) {
                $operations[] = [
                    self::OPERATION_ADDED,
                    $to[$index],
                ];
            }
        } elseif ($toStart === $toEnd) {
            for ($index = $fromStart; $index < $fromEnd; ++$index) {
                $operations[] = [
                    self::OPERATION_REMOVED,
                    $from[$index],
                ];
            }
        } else {
            $snake = self::middleSnake(
                $from,
                $fromStart,
                $fromEnd,
                $to,
                $toStart,
                $toEnd,
            );

            $before = self::diffRange(
                $from,
                $fromStart,
                $fromStart + $snake[0],
                $to,
                $toStart,
                $toStart + $snake[1],
            );

            foreach ($before as $operation) {
                $operations[] = $operation;
            }

            for ($index = $snake[0]; $index < $snake[2]; ++$index) {
                $operations[] = [
                    self::OPERATION_UNCHANGED,
                    $from[$fromStart + $index],
                ];
            }

            $after = self::diffRange(
                $from,
                $fromStart + $snake[2],
                $fromEnd,
                $to,
                $toStart + $snake[3],
                $toEnd,
            );

            foreach ($after as $operation) {
                $operations[] = $operation;
            }
        }

        foreach (\array_reverse($suffix) as $operation) {
            $operations[] = $operation;
        }

        return $operations;
    }

    /**
     * Finds the middle snake of an optimal path through the edit graph with a search from both ends.
     *
     * @param list<string> $from
     * @param list<string> $to
     *
     * @return array{0: int, 1: int, 2: int, 3: int}
     */
    private static function middleSnake(
        array $from,
        int $fromStart,
        int $fromEnd,
        array $to,
        int $toStart,
        int $toEnd
    ): array {
        $fromLength = $fromEnd - $fromStart;
        $toLength = $toEnd - $toStart;
        $delta = $fromLength - $toLength;
        $deltaIsOdd = 0 !== ($delta & 1);

        /**
         * diffRange() strips the common prefix and suffix before it calls this method, so the first lines and the last lines differ, and the search in both directions ends on diagonal 0 without a snake for distance 0.
         */
        $forward = [
            0 => 0,
        ];
        $backward = [
            0 => 0,
        ];
        $distance = 1;

        while (true) {
            for ($diagonal = -$distance; $diagonal <= $distance; $diagonal += 2) {
                if (
                    -$distance === $diagonal
                    || (
                        $distance !== $diagonal
                        && $forward[$diagonal - 1] < $forward[$diagonal + 1]
                    )
                ) {
                    $x = $forward[$diagonal + 1];
                } else {
                    $x = $forward[$diagonal - 1] + 1;
                }

                $y = $x - $diagonal;
                $xStart = $x;
                $yStart = $y;

                while (
                    $x < $fromLength
                    && $y < $toLength
                    && $from[$fromStart + $x] === $to[$toStart + $y]
                ) {
                    ++$x;
                    ++$y;
                }

                $forward[$diagonal] = $x;

                if (!$deltaIsOdd) {
                    continue;
                }

                $backwardDiagonal = $delta - $diagonal;

                if (
                    \array_key_exists($backwardDiagonal, $backward)
                    && $forward[$diagonal] + $backward[$backwardDiagonal] >= $fromLength
                ) {
                    return [
                        $xStart,
                        $yStart,
                        $x,
                        $y,
                    ];
                }
            }

            for ($diagonal = -$distance; $diagonal <= $distance; $diagonal += 2) {
                if (
                    -$distance === $diagonal
                    || (
                        $distance !== $diagonal
                        && $backward[$diagonal - 1] < $backward[$diagonal + 1]
                    )
                ) {
                    $x = $backward[$diagonal + 1];
                } else {
                    $x = $backward[$diagonal - 1] + 1;
                }

                $y = $x - $diagonal;
                $xStart = $x;
                $yStart = $y;

                while (
                    $x < $fromLength
                    && $y < $toLength
                    && $from[$fromEnd - 1 - $x] === $to[$toEnd - 1 - $y]
                ) {
                    ++$x;
                    ++$y;
                }

                $backward[$diagonal] = $x;

                if ($deltaIsOdd) {
                    continue;
                }

                $forwardDiagonal = $delta - $diagonal;

                if (
                    \array_key_exists($forwardDiagonal, $forward)
                    && $forward[$forwardDiagonal] + $backward[$diagonal] >= $fromLength
                ) {
                    return [
                        $fromLength - $x,
                        $toLength - $y,
                        $fromLength - $xStart,
                        $toLength - $yStart,
                    ];
                }
            }

            ++$distance;
        }
    }

    /**
     * Groups changed operations into hunks, merging hunks separated by fewer unchanged lines than two hunks would show as context.
     *
     * @param list<array{0: string, 1: string}> $operations
     *
     * @return list<array{0: int, 1: int}>
     */
    private static function hunks(array $operations): array
    {
        $changes = \array_keys(\array_filter($operations, static function (array $operation): bool {
            return self::OPERATION_UNCHANGED !== $operation[0];
        }));

        $ranges = [];

        foreach ($changes as $change) {
            $last = \count($ranges) - 1;

            if (
                [] !== $ranges
                && $change - $ranges[$last][1] - 1 < 2 * self::CONTEXT_LINES
            ) {
                $ranges[$last][1] = $change;

                continue;
            }

            $ranges[] = [
                $change,
                $change,
            ];
        }

        $hunks = [];

        foreach ($ranges as [$start, $end]) {
            $hunks[] = self::hunkBoundaries(
                $operations,
                $start,
                $end,
            );
        }

        return $hunks;
    }

    /**
     * @param list<array{0: string, 1: string}> $operations
     *
     * @return array{0: int, 1: int}
     */
    private static function hunkBoundaries(
        array $operations,
        int $start,
        int $end
    ): array {
        return [
            \max(
                0,
                $start - self::CONTEXT_LINES,
            ),
            \min(
                \count($operations) - 1,
                $end + self::CONTEXT_LINES,
            ),
        ];
    }

    /**
     * @param list<array{0: string, 1: string}> $operations
     */
    private static function hunk(
        array $operations,
        int $start,
        int $end,
        int $fromLinesBefore,
        int $toLinesBefore
    ): string {
        $fromRange = 0;
        $toRange = 0;
        $lines = '';

        for ($index = $start; $index <= $end; ++$index) {
            $operation = $operations[$index][0];
            $line = $operations[$index][1];

            if (self::OPERATION_ADDED !== $operation) {
                ++$fromRange;
            }

            if (self::OPERATION_REMOVED !== $operation) {
                ++$toRange;
            }

            $lines .= $operation . $line;

            if ("\n" !== $line[-1]) {
                $lines .= "\n\\ No newline at end of file\n";
            }
        }

        return \sprintf(
            "@@ -%s +%s @@\n%s",
            self::range(
                $fromLinesBefore,
                $fromRange,
            ),
            self::range(
                $toLinesBefore,
                $toRange,
            ),
            $lines,
        );
    }

    private static function range(
        int $linesBefore,
        int $length
    ): string {
        if (1 === $length) {
            return (string) ($linesBefore + 1);
        }

        return \sprintf(
            '%d,%d',
            $linesBefore + 1,
            $length,
        );
    }
}
