<?php

use Morilog\Jalali\Jalalian;

require __DIR__.'/../vendor/autoload.php';
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 20:15
 */

class OtherHelpers
{
    public static function convert_fa_num_to_en($number)
    {
        $en_num = array('0','1','2','3','4','5','6','7','8','9');
        $fa_num = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
        return str_replace($fa_num, $en_num, $number);
    }

    public static function parse_jalali_datetime_string_to_jalalian($string)
    {
        $tmp = explode(' ',$string);
        $date = explode('/',$tmp[0]);
        $time = explode(':',$tmp[1]);

        return new Jalalian($date[0],$date[1],$date[2],$time[0],$time[1],$time[2]);
    }
}