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

use Ergebnis\Differ;

final class MyersDifferBench
{
    private Differ\MyersDiffer $differ;
    private Differ\From $from;
    private Differ\To $to;

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

        $this->differ = new Differ\MyersDiffer();
        $this->from = Differ\From::create(
            Differ\Label::fromString('from'),
            Differ\Text::fromString(\implode('', $fromLines)),
        );
        $this->to = Differ\To::create(
            Differ\Label::fromString('to'),
            Differ\Text::fromString(\implode('', $toLines)),
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
