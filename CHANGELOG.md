## 2.5

 * Add support for Symfony 8, drop support for Symfony 6.4 and 7.0 to 7.3
 * Drop support for PHP 8.1, support PHP 8.2 and later
 * Bump minimum Twig version to 3.21
 * Improved internal code style

## 2.4.1

 * Fix hyperlink URL handling after changes introduced in phpoffice/phpspreadsheet v5.4
 * Add compatibility with phpoffice/phpspreadsheet v5.4 and newer

## 2.4

 * Add support for PHP 8.5
 * Fix deprecations from Twig 3.15 and 3.21
 * Set `phpoffice/phpspreadsheet` compatibility range to v1.23 to v5.3

## 2.3.1

 * Fix setting context on macro references

## 2.3

 * Add support for PHP 8.4

## 2.2

 * Drop support for Symfony 5 and lower, support Symfony 6.4, 7.0 and later
 * Bump minimum Twig version to 3.12

## 2.1

 * Declare conflict with twig/twig 3.9 to 3.11 due to bugs
 * Improved internal code style

## 2.0

 * Drop support for Symfony 4 and lower, support Symfony 5.4, 6.0 and later
 * Drop support for Twig 2, add support for Twig 3
 * Add support for dompdf as a PDF renderer
 * Add support for TCPDF as a PDF renderer
 * Improved internal code style

## 1.1

 * Fixed issue #13 'Incorrect cache directory for images' (technetium)
 * Fixed issue #15 'bug pre_calculate_formulas' (mikysan)
 * Fixed 'xlsmergestyles' which merged non-array values instead of overriding them
 * Added 'xlscellindex' and 'xlsrowindex' functions to get the current position
 * Added advanced CSV writer configurations like custom value separators etc
 * Improved internal index handling
 * Improved code performance

## 1.0

 * Removed xlsblock, xlsinclude and xlsmacro. The native Twig functions do work now like they are supposed to do!
 * Removed explicitValue cell property. Use dataType instead. However PhpSpread is handling leading zeros properly now so you probably don't have to use this property anyway.
 * Changed header/footer types. Just use 'even', 'odd', 'first' now.
 * ["Column indexes are now based on 1. So column A is the index 1. This is consistent with rows starting at 1 and Excel function COLUMN() that returns 1 for column A."](https://phpspreadsheet.readthedocs.io/en/develop/topics/migration-from-PHPExcel/#column-index-based-on-1)
