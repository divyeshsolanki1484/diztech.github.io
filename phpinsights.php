<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses;
use SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff;
use NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh;

return [
    'preset' => 'default',
    'exclude' => [
        'Service/ConfigService.php',
        'Resources/snippet/de_DE/SnippetFile_de_DE.php',
        'Resources/snippet/nl_NL/SnippetFile_nl_NL.php',
        'Resources/snippet/en_GB/SnippetFile_en_GB.php'
    ],
    'add' => [
    ],
    'remove' => [
        SpaceAfterNotSniff::class,
        ForbiddenNormalClasses::class,
        FunctionLengthSniff::class,
        BracesFixer::class,
        NoTrailingWhitespaceFixer::class,
        SuperfluousWhitespaceSniff::class,
        SuperfluousExceptionNamingSniff::class,
        SuperfluousInterfaceNamingSniff::class,
        AssignmentInConditionSniff::class,
        ForbiddenSetterSniff::class,
    ],
    'config' => [
        UnusedParameterSniff::class => [
            'exclude' => [
            ],
        ],
        CyclomaticComplexityIsHigh::class => [
            'maxComplexity' => 15,
        ],
        LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 160
        ]
    ],
];
