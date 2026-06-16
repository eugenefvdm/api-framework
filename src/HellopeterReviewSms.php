<?php

namespace Eugenefvdm\Api;

class HellopeterReviewSms
{
    public static function shorten(string $message, int $maxLength = 70): string
    {
        $pattern = '/^You received a (.+?) review by (.+?) at Hellopeter\. Please reply ASAP\.(.*)$/us';

        if (! preg_match($pattern, $message, $matches)) {
            return self::limit($message, $maxLength);
        }

        $stars = $matches[1];
        $name = trim($matches[2]);
        $compact = $stars.' review by '.$name.' @ Hellopeter. Reply ASAP.';

        if (mb_strlen($compact, 'UTF-8') <= $maxLength) {
            return $compact;
        }

        $prefix = $stars.' review by ';
        $suffix = ' @ Hellopeter. Reply ASAP.';
        $nameMaxLength = $maxLength - mb_strlen($prefix, 'UTF-8') - mb_strlen($suffix, 'UTF-8');

        if ($nameMaxLength < 4) {
            return self::limit($compact, $maxLength);
        }

        $truncatedName = mb_substr($name, 0, $nameMaxLength - 3, 'UTF-8').'...';

        return $prefix.$truncatedName.$suffix;
    }

    public static function starText(string $message): string
    {
        $replaced = preg_replace_callback(
            '/(\x{2B50}\x{FE0F}?)+/u',
            function (array $matches): string {
                $count = (int) preg_match_all('/\x{2B50}/u', $matches[0]);

                return $count.' star';
            },
            $message
        );

        return $replaced ?? $message;
    }

    private static function limit(string $message, int $maxLength): string
    {
        if ($maxLength < 1) {
            throw new \InvalidArgumentException('Maximum SMS length must be at least 1 character');
        }

        if (mb_strlen($message, 'UTF-8') <= $maxLength) {
            return $message;
        }

        if ($maxLength <= 3) {
            return mb_substr($message, 0, $maxLength, 'UTF-8');
        }

        return mb_substr($message, 0, $maxLength - 3, 'UTF-8').'...';
    }
}
