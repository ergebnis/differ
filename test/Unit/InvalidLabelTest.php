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

namespace Ergebnis\Differ\Test\Unit;

use Ergebnis\Differ\InvalidLabel;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Differ\InvalidLabel
 */
final class InvalidLabelTest extends Framework\TestCase
{
    public function testContainsLineBreakReturnsInvalidLabel(): void
    {
        $exception = InvalidLabel::containsLineBreak();

        self::assertSame('Label must not contain a line break.', $exception->getMessage());
    }
}
