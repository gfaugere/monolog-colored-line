<?php

declare(strict_types=1);

namespace gfaugere\Monolog\Formatter;

use DateTimeImmutable;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class ColoredLineFormatterTest extends TestCase
{
    public function testDefaultFormat()
    {
        $formatter = new ColoredLineFormatter(null, 'Y-m-d');
        $message = $formatter->format([
            'level_name' => 'WARNING',
            'level' => Logger::WARNING,
            'channel' => 'log',
            'context' => [],
            'message' => 'foo',
            'datetime' => new DateTimeImmutable(),
            'extra' => [],
        ]);
        $this->assertEquals('[' . date('Y-m-d') . "] log.\033[1;33mWARNING\033[0m: foo [] []\n", $message);
    }

    public function testFormatWithProvidedColor()
    {
        $formatter = new ColoredLineFormatter(
            "%color_start%[%datetime%] %channel%.%level_name%:%color_end% %message% %context% %extra%\n",
            'Y-m-d'
        );
        $message = $formatter->format([
            'level_name' => 'WARNING',
            'level' => Logger::WARNING,
            'channel' => 'log',
            'context' => [],
            'message' => 'foo',
            'datetime' => new DateTimeImmutable(),
            'extra' => [],
        ]);
        $this->assertEquals("\033[1;33m[" . date('Y-m-d') . "] log.WARNING:\033[0m foo [] []\n", $message);
    }

    public function testCustomColorScheme()
    {
        $scheme = [
            Logger::DEBUG     => "\033[38;5;206m",
            Logger::INFO      => "\033[38;5;196m",
            Logger::NOTICE    => "\033[38;5;202m",
            Logger::WARNING   => "\033[38;5;226m",
            Logger::ERROR     => "\033[38;5;34m",
            Logger::CRITICAL  => "\033[38;5;81m",
            Logger::ALERT     => "\033[38;5;53m",
            Logger::EMERGENCY => "\033[38;5;129m"
        ];
        $formatter = new ColoredLineFormatter('%level_name%', null, false, false, $scheme);

        $this->assertEquals(
            "\033[38;5;206mDEBUG\033[0m",
            $formatter->format(['level_name' => 'DEBUG', 'level' => Logger::DEBUG, 'context' => [], 'extra' => []])
        );
        $this->assertEquals(
            "\033[38;5;196mINFO\033[0m",
            $formatter->format(['level_name' => 'INFO', 'level' => Logger::INFO, 'context' => [], 'extra' => []])
        );
        $this->assertEquals(
            "\033[38;5;202mNOTICE\033[0m",
            $formatter->format(['level_name' => 'NOTICE', 'level' => Logger::NOTICE, 'context' => [], 'extra' => []])
        );
        $this->assertEquals(
            "\033[38;5;226mWARNING\033[0m",
            $formatter->format(['level_name' => 'WARNING', 'level' => Logger::WARNING, 'context' => [], 'extra' => []])
        );
        $this->assertEquals(
            "\033[38;5;34mERROR\033[0m",
            $formatter->format(['level_name' => 'ERROR', 'level' => Logger::ERROR, 'context' => [], 'extra' => []])
        );
        $this->assertEquals(
            "\033[38;5;81mCRITICAL\033[0m",
            $formatter->format(['level_name' => 'CRITICAL', 'level' => Logger::CRITICAL, 'context' => [], 'extra' => []])
        );
        $this->assertEquals(
            "\033[38;5;53mALERT\033[0m",
            $formatter->format(['level_name' => 'ALERT', 'level' => Logger::ALERT, 'context' => [], 'extra' => []])
        );
        $this->assertEquals(
            "\033[38;5;129mEMERGENCY\033[0m",
            $formatter->format(['level_name' => 'EMERGENCY', 'level' => Logger::EMERGENCY, 'context' => [], 'extra' => []])
        );
    }

    public function testColorLevelFirst()
    {
        $formatter = new ColoredLineFormatter(
            "%level_name% %level_name% %level_name%\n",
            'Y-m-d',
            false,
            false,
            null,
            ColoredLineFormatter::MODE_COLOR_LEVEL_FIRST
        );
        $message = $formatter->format([
            'level_name' => 'WARNING',
            'level' => Logger::WARNING,
            'channel' => 'log',
            'context' => [],
            'message' => 'foo',
            'datetime' => new DateTimeImmutable(),
            'extra' => [],
        ]);
        $this->assertEquals("\033[1;33mWARNING\033[0m WARNING WARNING\n", $message);
    }
}
