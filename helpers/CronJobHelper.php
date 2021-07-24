<?php

use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/24/21
 * Time: 03:54
 */

class CronJobHelper
{
    public static function get_new_reports(array $LetterCode = [])
    {
        $last_record = self::get_last_report_inserted_to_db();

        $page = 1;

        $search = new CodalSearch();

        while (true){
            $isOnThisPage = false;

            $letters = $search->search($page)->result->Letters;

            foreach ($letters as $item){
                if ($item->TracingNo == $last_record['tracking_no']){
                    $isOnThisPage = true;
                    break;
                }
            }

            if ($isOnThisPage){
                break;
            }

            $page++;
        }

        for ($i = $page;$i >= 1;$i--){
            $search->search($i)->get_result()->addLetterCodeFilter($LetterCode)->store();
        }
    }

    public static function fetch_companies(){
        $count = Company::count();

        if ($count == 0){
            CodalSearch::get_companies();
        }else{
            if (Carbon::now('Asia/Tehran')->diffInMinutes(Carbon::now('Asia/Tehran')->setHour(0)->setMinute(0)) <= 8){
                CodalSearch::get_companies();
            }
        }
    }

    private static function get_last_report_inserted_to_db()
    {
        return Report::orderBy('publish_time','DESC')->first()->toArray();
    }
}