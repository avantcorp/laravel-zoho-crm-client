<?php

declare(strict_types=1);

if (!function_exists('sanitise_phone')) {
    function sanitise_phone(?string $phone): ?string
    {
        if ($phone) {
            $phone = preg_replace('/^00/', '+', trim($phone));
            $hasPlus = strpos($phone, '+') === 0;
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if ($hasPlus) {
                $phone = "+{$phone}";
            }
            $phone = preg_replace('/^0([1-9][0-9]{9})$/', '+44$1', $phone);
        }

        return $phone ?: null;
    }
}
