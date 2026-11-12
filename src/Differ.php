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

interface Differ
{
    /**
     * Returns a unified diff of the lines of the texts of $from and $to, which is empty when the texts are identical.
     */
    public function diff(
        From $from,
        To $to
    ): Diff;
}
