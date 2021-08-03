<?php

use Carbon\Carbon;

require 'vendor/autoload.php';
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 8/2/21
 * Time: 17:48
 */
$start = Carbon::now();

$report_id = 40;

CodalN10Helper::get($report_id);

echo Carbon::now()->diffInSeconds($start).PHP_EOL;