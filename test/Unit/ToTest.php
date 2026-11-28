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
 * @covers \Ergebnis\Differ\To
 *
 * @uses \Ergebnis\Differ\Label
 * @uses \Ergebnis\Differ\Text
 */
final class ToTest extends Framework\TestCase
{
    use Differ\Test\Util\Helper;

    public function testCreateReturnsTo(): void
    {
        $faker = self::faker();

        $label = Differ\Label::fromString($faker->word());
        $text = Differ\Text::fromString($faker->text());

        $to = Differ\To::create(
            $label,
            $text,
        );

        self::assertSame($label, $to->label());
        self::assertSame($text, $to->text());
    }
}
