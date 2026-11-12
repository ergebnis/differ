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

namespace Ergebnis\Differ\Test\Benchmark;

use Ergebnis\Differ\From;
use Ergebnis\Differ\Label;
use Ergebnis\Differ\MyersDiffer;
use Ergebnis\Differ\Text;
use Ergebnis\Differ\To;

final class MyersDifferBench
{
    private MyersDiffer $differ;
    private From $from;
    private To $to;

    /**
     * @param array{change: string, lines: int} $parameters
     */
    public function setUp(array $parameters): void
    {
        $fromLines = [];
        $toLines = [];

        for ($index = 0; $index < $parameters['lines']; ++$index) {
            $line = \sprintf(
                "line %d\n",
                $index,
            );

            $fromLines[] = $line;
            $toLines[] = $line;
        }

        if ('first-line' === $parameters['change']) {
            $toLines[0] = "changed\n";
        }

        if ('every-50th-line' === $parameters['change']) {
            for ($index = 0; $index < $parameters['lines']; $index += 50) {
                $toLines[$index] = \sprintf(
                    "changed %d\n",
                    $index,
                );
            }
        }

        if ('every-line' === $parameters['change']) {
            foreach ($toLines as $index => $line) {
                $toLines[$index] = \sprintf(
                    "changed %d\n",
                    $index,
                );
            }
        }

        $this->differ = new MyersDiffer();
        $this->from = From::create(
            Label::fromString('from'),
            Text::fromString(\implode('', $fromLines)),
        );
        $this->to = To::create(
            Label::fromString('to'),
            Text::fromString(\implode('', $toLines)),
        );
    }

    /**
     * @return \Generator<string, array{change: string, lines: int}>
     */
    public function provideChangeAndLines(): iterable
    {
        $changes = [
            'first-line',
            'every-50th-line',
            'every-line',
        ];

        $counts = [
            1000,
            10000,
        ];

        foreach ($changes as $change) {
            foreach ($counts as $lines) {
                $key = \sprintf(
                    '%s-%d',
                    $change,
                    $lines,
                );

                yield $key => [
                    'change' => $change,
                    'lines' => $lines,
                ];
            }
        }
    }

    /**
     * @BeforeMethods("setUp")
     *
     * @ParamProviders("provideChangeAndLines")
     */
    public function benchDiff(): void
    {
        $this->differ->diff(
            $this->from,
            $this->to,
        );
    }
}
