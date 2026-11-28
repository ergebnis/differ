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

use Ergebnis\Differ;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Differ\Diff
 */
final class DiffTest extends Framework\TestCase
{
    use Differ\Test\Util\Helper;

    public function testFromStringReturnsDiffWhenValueIsEmpty(): void
    {
        $diff = Differ\Diff::fromString('');

        self::assertTrue($diff->isEmpty());
        self::assertSame('', $diff->toString());
    }

    public function testFromStringReturnsDiffWhenValueIsNotEmpty(): void
    {
        $value = self::faker()->text();

        $diff = Differ\Diff::fromString($value);

        self::assertFalse($diff->isEmpty());
        self::assertSame($value, $diff->toString());
    }
}
