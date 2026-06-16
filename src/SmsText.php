<?php

namespace Eugenefvdm\Api;

class SmsText
{
    private const GSM_7BIT_CHARACTERS = "@£\$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞ\x1BÆæßÉ !\"#¤%&'()*+,-./0123456789:;<=>?"
        .'¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà';

    private const GSM_7BIT_EXTENSION_CHARACTERS = '^{}\\[~]|€';

    public static function encoding(string $text): string
    {
        if (self::isGsmSafe($text)) {
            return Bulksms::ENCODING_7BIT;
        }

        return Bulksms::ENCODING_16BIT;
    }

    public static function isGsmSafe(string $text): bool
    {
        $text = self::utf8($text);

        for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
            $character = mb_substr($text, $i, 1, 'UTF-8');

            if (! str_contains(self::GSM_7BIT_CHARACTERS.self::GSM_7BIT_EXTENSION_CHARACTERS, $character)) {
                return false;
            }
        }

        return true;
    }

    public static function limit(string $text, string $encoding = Bulksms::ENCODING_7BIT, int $parts = 1): string
    {
        $encoding = self::normaliseEncoding($text, $encoding);
        $maxLength = self::maxLength($encoding, $parts, $text);

        return self::limitTo($text, $maxLength, $encoding);
    }

    public static function limitTo(string $text, int $maxLength, string $encoding = Bulksms::ENCODING_AUTO): string
    {
        if ($maxLength < 1) {
            throw new \InvalidArgumentException('SMS length must be at least 1');
        }

        $encoding = self::normaliseEncoding($text, $encoding);

        if (self::length($text, $encoding) <= $maxLength) {
            return $text;
        }

        $suffix = '...';
        $suffixLength = self::length($suffix, $encoding);

        if ($maxLength <= 3) {
            return self::trimToLength($text, $maxLength, $encoding);
        }

        return self::trimToLength($text, $maxLength - $suffixLength, $encoding).$suffix;
    }

    public static function maxLength(
        string $encoding = Bulksms::ENCODING_7BIT,
        int $parts = 1,
        string $text = '',
    ): int {
        if ($parts < 1) {
            throw new \InvalidArgumentException('SMS parts must be at least 1');
        }

        $encoding = self::normaliseEncoding($text, $encoding);

        return match ($encoding) {
            Bulksms::ENCODING_7BIT => ($parts === 1 ? 160 : 153 * $parts),
            Bulksms::ENCODING_16BIT => ($parts === 1 ? 70 : 67 * $parts),
            default => throw new \InvalidArgumentException("Unsupported SMS encoding [$encoding]"),
        };
    }

    public static function length(string $text, string $encoding = Bulksms::ENCODING_AUTO): int
    {
        $encoding = self::normaliseEncoding($text, $encoding);

        if ($encoding === Bulksms::ENCODING_16BIT) {
            return mb_strlen($text, 'UTF-8');
        }

        $length = 0;
        foreach (self::characters($text) as $character) {
            $length += str_contains(self::GSM_7BIT_EXTENSION_CHARACTERS, $character) ? 2 : 1;
        }

        return $length;
    }

    /**
     * @return array<int, string>
     */
    private static function characters(string $text): array
    {
        $text = self::utf8($text);

        $characters = [];
        for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
            $characters[] = mb_substr($text, $i, 1, 'UTF-8');
        }

        return $characters;
    }

    private static function utf8(string $text): string
    {
        if (mb_detect_encoding($text, 'UTF-8') === 'UTF-8') {
            return $text;
        }

        $converted = mb_convert_encoding($text, 'UTF-8', 'auto');

        if ($converted === false) {
            throw new \RuntimeException('Failed to convert SMS text encoding to UTF-8');
        }

        return $converted;
    }

    private static function normaliseEncoding(string $text, string $encoding): string
    {
        $encoding = strtolower($encoding);

        if ($encoding === Bulksms::ENCODING_AUTO) {
            return self::encoding($text);
        }

        if (! in_array($encoding, [Bulksms::ENCODING_7BIT, Bulksms::ENCODING_16BIT], true)) {
            throw new \InvalidArgumentException("Unsupported SMS encoding [$encoding]");
        }

        return $encoding;
    }

    private static function trimToLength(string $text, int $maxLength, string $encoding): string
    {
        $trimmed = '';
        $length = 0;

        foreach (self::characters($text) as $character) {
            $characterLength = $encoding === Bulksms::ENCODING_7BIT
                && str_contains(self::GSM_7BIT_EXTENSION_CHARACTERS, $character) ? 2 : 1;

            if ($length + $characterLength > $maxLength) {
                return $trimmed;
            }

            $trimmed .= $character;
            $length += $characterLength;
        }

        return $trimmed;
    }
}
