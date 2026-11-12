# differ

[![Integrate](https://github.com/ergebnis/differ/actions/workflows/integrate.yaml/badge.svg?branch=main)](https://github.com/ergebnis/differ/actions/workflows/integrate.yaml)
[![Merge](https://github.com/ergebnis/differ/actions/workflows/merge.yaml/badge.svg)](https://github.com/ergebnis/differ/actions/workflows/merge.yaml)
[![Release](https://github.com/ergebnis/differ/actions/workflows/release.yaml/badge.svg)](https://github.com/ergebnis/differ/actions/workflows/release.yaml)
[![Renew](https://github.com/ergebnis/differ/actions/workflows/renew.yaml/badge.svg)](https://github.com/ergebnis/differ/actions/workflows/renew.yaml)

[![Code Coverage](https://codecov.io/gh/ergebnis/differ/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/differ)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/differ/v/stable)](https://packagist.org/packages/ergebnis/differ)
[![Total Downloads](https://poser.pugx.org/ergebnis/differ/downloads)](https://packagist.org/packages/ergebnis/differ)
[![Monthly Downloads](https://poser.pugx.org/ergebnis/differ/d/monthly)](https://packagist.org/packages/ergebnis/differ)

This project provides a [`composer`](https://getcomposer.org) package with a differ for creating a unified diff of two strings.

## Installation

Run

```sh
composer require ergebnis/differ
```

## Usage

Create a `MyersDiffer`, then call `diff()` with a `From` and a `To`, each made of a `Label` and a `Text`, to receive a `Diff` of the lines of both `Text`s:

```php
<?php

declare(strict_types=1);

use Ergebnis\Differ;

$differ = new Differ\MyersDiffer();

$diff = $differ->diff(
    Differ\From::create(
        Differ\Label::fromString('original'),
        Differ\Text::fromString("foo\nbar\nbaz\n"),
    ),
    Differ\To::create(
        Differ\Label::fromString('changed'),
        Differ\Text::fromString("foo\nqux\nbaz\n"),
    ),
);

echo $diff->toString();
```

This prints

```diff
--- original
+++ changed
@@ -1,3 +1,3 @@
 foo
-bar
+qux
 baz
```

When both `Text`s are identical, `Diff::isEmpty()` returns `true` and `Diff::toString()` returns an empty string.

`MyersDiffer` implements the `Differ` interface, which you can use as a type declaration.

`Label::fromString()` throws an `InvalidLabel` exception when the value contains a line break, because a line break would break the header of the diff.

When both `Text`s end with a new line, `Diff::toString()` returns the same unified diff as [`sebastian/diff`](https://github.com/sebastianbergmann/diff) with its `StrictUnifiedDiffOutputBuilder`.

## Changelog

The maintainers of this project record notable changes to this project in a [changelog](CHANGELOG.md).

## Contributing

The maintainers of this project suggest following the [contribution guide](.github/CONTRIBUTING.md).

## Code of Conduct

The maintainers of this project ask contributors to follow the [code of conduct](.github/CODE_OF_CONDUCT.md).

## General Support Policy

The maintainers of this project provide limited support.

## PHP Version Support Policy

This project currently supports the following PHP versions:

- [PHP 7.4](https://www.php.net/releases/#7.4.0) (has reached its end of life on November 28, 2022)
- [PHP 8.0](https://www.php.net/releases/#8.0.0) (has reached its end of life on November 26, 2023)
- [PHP 8.1](https://www.php.net/releases/#8.1.0) (has reached its end of life on December 31, 2025)
- [PHP 8.2](https://www.php.net/releases/#8.2.0)
- [PHP 8.3](https://www.php.net/releases/#8.3.0)
- [PHP 8.4](https://www.php.net/releases/#8.4.0)
- [PHP 8.5](https://www.php.net/releases/#8.5.0)
- [PHP 8.6](https://www.php.net/releases/#8.6.0)

The maintainers of this project add support for a PHP version following its initial release and _may_ drop support for a PHP version when it has reached its [end of life](https://www.php.net/supported-versions.php).

## Security Policy

This project has a [security policy](.github/SECURITY.md).

## License

This project uses the [MIT license](LICENSE.md).

## Credits

The algorithm for computing the diff in [`MyersDiffer`](src/MyersDiffer.php) has been adopted from [`SebastianBergmann\Diff\MyersDiff`](https://github.com/sebastianbergmann/diff/blob/9.0.1/src/MyersDiff.php) and [`SebastianBergmann\Diff\Output\StrictUnifiedDiffOutputBuilder`](https://github.com/sebastianbergmann/diff/blob/9.0.1/src/Output/StrictUnifiedDiffOutputBuilder.php) (originally licensed under BSD-3-Clause by [Sebastian Bergmann](https://github.com/sebastianbergmann)), which implement the linear-space variant of the algorithm described by Eugene W. Myers in ["An O(ND) Difference Algorithm and Its Variations"](https://doi.org/10.1007/BF01840446).

## Social

Follow [@localheinz](https://x.com/intent/follow?screen_name=localheinz) and [@ergebnis](https://x.com/intent/follow?screen_name=ergebnis) on X.
