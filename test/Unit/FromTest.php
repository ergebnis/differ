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

use Ergebnis\Differ\From;
use Ergebnis\Differ\Label;
use Ergebnis\Differ\Test;
use Ergebnis\Differ\Text;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Differ\From
 *
 * @uses \Ergebnis\Differ\Label
 * @uses \Ergebnis\Differ\Text
 */
final class FromTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsFrom(): void
    {
        $faker = self::faker();

        $label = Label::fromString($faker->word());
        $text = Text::fromString($faker->text());

        $from = From::create(
            $label,
            $text,
        );

        self::assertSame($label, $from->label());
        self::assertSame($text, $from->text());
    }
}
