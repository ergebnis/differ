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

final class From
{
    private Label $label;
    private Text $text;

    private function __construct(
        Label $label,
        Text $text
    ) {
        $this->label = $label;
        $this->text = $text;
    }

    public static function create(
        Label $label,
        Text $text
    ): self {
        return new self(
            $label,
            $text,
        );
    }

    public function label(): Label
    {
        return $this->label;
    }

    public function text(): Text
    {
        return $this->text;
    }
}
