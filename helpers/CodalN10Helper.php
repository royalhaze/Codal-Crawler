<?php

use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 8/2/21
 * Time: 17:49
 */

class CodalN10Helper
{
    public static function get($report_id)
    {
        $report = Report::findOrFail($report_id);
        //validate
        if ($report->letter_code != 'ن-۱۰'){
            throw new \Exception('اطلاعیه از نوع ن ۱۰ نمیباشد');
        }
        //get report url
        $page_url = ReportData::where('report_id',$report_id)->where('title','ReportUrl')->firstOrFail()->value;

        //start store functions
        $store = new PageMetaDataHelper($page_url,$report_id);

        $store->get_data()->store();
    }

    public static function cronjob()
    {
        $n10s = Report::where('letter_code','ن-۱۰')->where('symbol_id','!=',null)->where('crawl_time','>',Carbon::now('Asia/Tehran')->subMinutes(AppConfig::N10SEARCHTIME))->orderBy('id','DESC')->get();

        foreach ($n10s as $item){
            $has_decision = Decision::where('report_id',$item->id)->count();

            if ($has_decision == 0){
                self::get($item->id);
            }
        }

    }
}