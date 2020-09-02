<?php

declare(strict_types=1);

namespace gfaugere\Monolog\Formatter;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;

class ColoredLineFormatter extends LineFormatter
{
    public const MODE_COLOR_LEVEL_ALL = -1;
    public const MODE_COLOR_LEVEL_FIRST = 1;

    private const RESET = "\033[0m";

    /**
     * Color scheme - use ANSI colour sequences
     * @var string[]
     */
    private $colorScheme = [
        Logger::DEBUG     => "\033[0;37m",
        Logger::INFO      => "\033[1;37m",
        Logger::NOTICE    => "\033[1;34m",
        Logger::WARNING   => "\033[1;33m",
        Logger::ERROR     => "\033[0;33m",
        Logger::CRITICAL  => "\033[1;31m",
        Logger::ALERT     => "\033[0;31m",
        Logger::EMERGENCY => "\033[1;35m"
    ];

    /**
     * ColoredLineFormatter constructor.
     * @param string|null $format                     The format of the message
     * @param string|null $dateFormat                 The format of the timestamp: one supported by DateTime::format
     * @param bool        $allowInlineLineBreaks      Whether to allow inline line breaks in log entries
     * @param bool        $ignoreEmptyContextAndExtra
     * @param array<string>|null $colorScheme         @see ColoredLineFormatter::$colorScheme
     * @param int $colorMode                Whether we want to replace all '%level_name%' occurrences or only the first.
     *                                                Only useful if no %color_start%/%color_end% specified in $format
     */
    public function __construct(
        ?string $format = LineFormatter::SIMPLE_FORMAT,
        ?string $dateFormat = null,
        bool $allowInlineLineBreaks = false,
        bool $ignoreEmptyContextAndExtra = false,
        ?array $colorScheme = null,
        int $colorMode = self::MODE_COLOR_LEVEL_ALL
    ) {
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);

        if (false === strpos($this->format, '%color_start%') && false === strpos($this->format, '%color_end%')) {
            $this->format = preg_replace(
                '/%level_name%/',
                '%color_start%%level_name%%color_end%',
                $this->format,
                $colorMode
            );
        }
        if (!is_null($colorScheme)) {
            $this->colorScheme = $colorScheme;
        }
    }

    /**
     * Formats a log record, with color.
     *
     * @param  array $record A record to format
     * @return string The formatted and colored record
     */
    public function format(array $record): string
    {
        $formatted = parent::format($record);
        $formatted = str_replace('%color_start%', $this->colorScheme[$record['level']], $formatted);
        $formatted = str_replace('%color_end%', self::RESET, $formatted);
        return $formatted;
    }
}
