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
 * @covers \Ergebnis\Differ\Text
 */
final class TextTest extends Framework\TestCase
{
    use Differ\Test\Util\Helper;

    public function testFromStringReturnsText(): void
    {
        $value = self::faker()->text();

        $text = Differ\Text::fromString($value);

        self::assertSame($value, $text->toString());
    }
}
