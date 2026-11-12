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

final class Label
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @throws InvalidLabel
     */
    public static function fromString(string $value): self
    {
        if (false !== \strpbrk($value, "\n\r")) {
            throw InvalidLabel::containsLineBreak();
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
