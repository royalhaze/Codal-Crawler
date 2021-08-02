<?php
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

        if ($report->letter_code != 'ن-۱۰'){
            throw new \Exception('اطلاعیه از نوع ن ۱۰ نمیباشد');
        }

        $page_url = ReportData::where('report_id',$report_id)->where('title','ReportUrl')->firstOrFail()->value;

        $store = new PageMetaDataHelper($page_url,$report_id);

        $store->get_data()->store();
    }
}