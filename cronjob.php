<?php
require 'vendor/autoload.php';

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/24/21
 * Time: 04:45
 */

CronJobHelper::get_new_reports(AppConfig::CronJobLetterCodeFilter());

CronJobHelper::fetch_companies();