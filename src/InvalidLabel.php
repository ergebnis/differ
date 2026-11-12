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

final class InvalidLabel extends \InvalidArgumentException implements Exception
{
    public static function containsLineBreak(): self
    {
        return new self('Label must not contain a line break.');
    }
}
