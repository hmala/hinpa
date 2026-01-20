<?php

namespace App\Helpers;

class NumberToArabicWords
{
    private static $digits = [
        0 => 'صفر',
        1 => 'واحد',
        2 => 'اثنان',
        3 => 'ثلاثة',
        4 => 'أربعة',
        5 => 'خمسة',
        6 => 'ستة',
        7 => 'سبعة',
        8 => 'ثمانية',
        9 => 'تسعة',
        10 => 'عشرة',
        11 => 'أحد عشر',
        12 => 'اثنا عشر',
        13 => 'ثلاثة عشر',
        14 => 'أربعة عشر',
        15 => 'خمسة عشر',
        16 => 'ستة عشر',
        17 => 'سبعة عشر',
        18 => 'ثمانية عشر',
        19 => 'تسعة عشر',
        20 => 'عشرون',
        30 => 'ثلاثون',
        40 => 'أربعون',
        50 => 'خمسون',
        60 => 'ستون',
        70 => 'سبعون',
        80 => 'ثمانون',
        90 => 'تسعون',
        100 => 'مائة',
        200 => 'مئتان',
        300 => 'ثلاثمائة',
        400 => 'أربعمائة',
        500 => 'خمسمائة',
        600 => 'ستمائة',
        700 => 'سبعمائة',
        800 => 'ثمانمائة',
        900 => 'تسعمائة'
    ];

    private static $hundreds = [
        1 => 'مائة',
        2 => 'مئتان',
        3 => 'ثلاثمائة',
        4 => 'أربعمائة',
        5 => 'خمسمائة',
        6 => 'ستمائة',
        7 => 'سبعمائة',
        8 => 'ثمانمائة',
        9 => 'تسعمائة'
    ];

    private static $thousands = [
        1 => 'ألف',
        2 => 'ألفان',
        3 => 'آلاف',
        11 => 'ألفاً'
    ];

    private static $millions = [
        1 => 'مليون',
        2 => 'مليونان',
        3 => 'ملايين',
        11 => 'مليوناً'
    ];

    public static function convert($number)
    {
        if ($number == 0) {
            return self::$digits[0];
        }

        // تحويل الرقم إلى سلسلة نصية وتقسيمه إلى صحيح وكسري
        $parts = explode('.', number_format($number, 2, '.', ''));
        $integer = intval($parts[0]);
        $fraction = isset($parts[1]) ? intval($parts[1]) : 0;

        $result = '';

        // تحويل الجزء الصحيح
        if ($integer > 0) {
            $result .= self::convertInteger($integer) . ' دينار';
            if ($integer != 1) {
                $result .= 'اً';
            }
        }

        // تحويل الجزء الكسري
        if ($fraction > 0) {
            if ($result != '') {
                $result .= ' و ';
            }
            $result .= self::convertInteger($fraction) . ' فلس';
            if ($fraction != 1) {
                $result .= 'اً';
            }
        }

        return $result;
    }

    private static function convertInteger($number)
    {
        if ($number < 20) {
            return self::$digits[$number];
        }

        if ($number < 100) {
            $unit = $number % 10;
            $ten = floor($number / 10) * 10;
            return $unit > 0 ? self::$digits[$unit] . ' و' . self::$digits[$ten] : self::$digits[$ten];
        }

        if ($number < 1000) {
            $hundred = floor($number / 100);
            $remainder = $number % 100;
            $result = self::$hundreds[$hundred];
            if ($remainder > 0) {
                $result .= ' و' . self::convertInteger($remainder);
            }
            return $result;
        }

        if ($number < 1000000) {
            $thousand = floor($number / 1000);
            $remainder = $number % 1000;
            
            if ($thousand == 2) {
                $result = self::$thousands[2];
            } elseif ($thousand <= 10) {
                $result = self::convertInteger($thousand) . ' ' . ($thousand == 1 ? self::$thousands[1] : self::$thousands[3]);
            } else {
                $result = self::convertInteger($thousand) . ' ' . self::$thousands[11];
            }
            
            if ($remainder > 0) {
                $result .= ' و' . self::convertInteger($remainder);
            }
            return $result;
        }

        $million = floor($number / 1000000);
        $remainder = $number % 1000000;
        
        if ($million == 2) {
            $result = self::$millions[2];
        } elseif ($million <= 10) {
            $result = self::convertInteger($million) . ' ' . ($million == 1 ? self::$millions[1] : self::$millions[3]);
        } else {
            $result = self::convertInteger($million) . ' ' . self::$millions[11];
        }
        
        if ($remainder > 0) {
            $result .= ' و' . self::convertInteger($remainder);
        }
        return $result;
    }
}
