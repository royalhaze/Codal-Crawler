<?php
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 8/1/21
 * Time: 19:54
 */

class StorePagesData
{
    private $headers,$pages_data,$report_id,$decision_id;

    const YEAR_LEADING_TO = 'سال مالی منتهی به';

    const REGISTERED_FOUND = 'سرمایه ثبت شده';

    const UNREGISTERED_FOUND = 'سرمایه ثبت نشده';

    const PUBLISHER_STATUS = 'وضعیت ناشر';

    public function __construct($headers_data,$pages_data,$report_id)
    {
        $this->headers = $headers_data;

        $this->pages_data = $pages_data;

        $this->report_id = $report_id;
    }

    public function store()
    {
        try{
            $this->store_headers();
            $this->store_pages_data();

            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    private function store_headers(){
        $data = [
            'year_leading_to' => $this->headers[self::YEAR_LEADING_TO],
            'registered_fund'  => (int) str_replace(',','',$this->headers[self::REGISTERED_FOUND]),
            'unregistered_fund' => (int) str_replace(',','',$this->headers[self::UNREGISTERED_FOUND]),
            'publisher_status' => $this->headers[self::PUBLISHER_STATUS]
        ];

        $company_data = $this->get_symbol_data();

        $data = array_merge($data,$company_data);

        $db = Decision::create($data);

        if (!$db instanceof Decision){
          throw new \Exception('Decision store failed');
        }else{
            $this->decision_id = $db->id;
        }

        return true;
    }

    private function store_pages_data(){
        foreach ($this->pages_data as $title => $item){
            if ($title != 'ترازنامه' && $title != 'نظر حسابرس'){
                $this->store_general_tables_data($item,$title);
            }elseif ($title == 'ترازنامه'){
                $this->store_taraznameh_table_data($item,$title);
            }elseif ($title == 'نظر حسابرس'){
                $this->store_nazare_hesabres($item);
            }
        }

        return true;
    }

    private function store_general_tables_data($data,$page_title){
        $items = [];
        $change_key = null;
        foreach ($data['header'][1] as $key => $value){
            if ($value != 'شرح' && $value != 'درصد
 تغییرات') {
                $items[$key] = $value;
            }
            if ($value == 'درصد
 تغییرات') {
                $change_key = $key;
            }
        }

        $data_to_store = [];

        foreach ($items as $key => $value){
            foreach ($data['data'] as $item){
                $data_to_store[] = [
                  'report_id' => $this->report_id,
                    'for_page' => $page_title,
                    'title' => $item[1],
                    'value' => (int)$item[$key],
                    'date_leading_to' => $value,
                    'change' => (int)$item[$change_key],
                    'is_for_this_year' => ($key == 2)?true:false,
                ];
            }
        }

        foreach ($data_to_store as $data){
            DecisionData::create($data);
        }
    }

    private function store_taraznameh_table_data($data,$page_title){
        $new_header = [];

        $header_count = count($data['header'][1])/2;

        for ($i = 1 ; $i <= $header_count ; $i++){
            if ($data['header'][1][$i] == 'پایان عملکرد واقعی منتهی به'){
                $new_header[$i] = $data['header'][1][$i].$data['header'][2][$i];
            }else{
                $new_header[$i] = $data['header'][1][$i];
            }
        }

        $data['header'][1] = $new_header;

        $this->store_general_tables_data($data,$page_title);
    }

    private function store_nazare_hesabres($data){
        ReportData::create([
           'report_id' => $this->report_id,
           'title' => 'نظر حسابرس',
           'value' => trim($data)
        ]);
    }

    private function get_symbol_data(){
        $report = Report::findOrFail($this->report_id);

        $company = Company::findOrFail($report->company_id);

        return [
          'company_id' => $company->id,
          'symbol_id' => $company->symbol_id,
          'symbol' => $company->symbol,
            'report_id' => $this->report_id
        ];
    }
}