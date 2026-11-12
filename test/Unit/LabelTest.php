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
use Ergebnis\Differ\Label;
use Ergebnis\Differ\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Differ\Label
 *
 * @uses \Ergebnis\Differ\InvalidLabel
 */
final class LabelTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideLineBreak
     */
    public function testFromStringThrowsInvalidLabelWhenValueContainsLineBreak(string $lineBreak): void
    {
        $faker = self::faker();

        $value = \sprintf(
            '%s%s%s',
            $faker->word(),
            $lineBreak,
            $faker->word(),
        );

        $this->expectException(InvalidLabel::class);

        Label::fromString($value);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideLineBreak(): iterable
    {
        yield 'carriage return' => [
            "\r",
        ];

        yield 'line feed' => [
            "\n",
        ];

        yield 'carriage return and line feed' => [
            "\r\n",
        ];
    }

    public function testFromStringReturnsLabelWhenValueDoesNotContainLineBreak(): void
    {
        $value = self::faker()->sentence();

        $label = Label::fromString($value);

        self::assertSame($value, $label->toString());
    }
}
