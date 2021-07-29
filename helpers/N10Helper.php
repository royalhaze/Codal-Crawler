<?php

use DiDom\Document;
use GuzzleHttp\Client;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/28/21
 * Time: 00:23
 */

class N10Helper
{
    private $pages_to_get = [
        'صورت سود و زیان',
        'نظر حسابرس',
        'صورت سود و زیان جامع',
        'صورت وضعیت مالی',
        'صورت جریان های نقدی',
        'جریان وجوه نقد',
        'ترازنامه'
    ];

    private $pages_to_get_en = [
        'صورت سود و زیان' => 'Income Statement',
        'صورت سود و زیان جامع' => 'Comprehensive Income Statement',
        'صورت وضعیت مالی' => 'Balance Sheet',
        'صورت جریان های نقدی' => 'Cash Flow',
        'جریان وجوه نقد' => 'Cash Flow',
        'ترازنامه' => 'Balance Sheet'
    ];

    private $pages_has_general_table = [
        'صورت سود و زیان',
        'صورت سود و زیان جامع',
        'صورت وضعیت مالی',
        'صورت جریان های نقدی',
        'جریان وجوه نقد',
    ];

    private $report_url,$pages;

    private $pages_content = [];

    public $company_type;

    public $page_header_content = [];

    public $data;

    public function __construct($report_url)
    {
        $this->report_url = $report_url;
    }

    private function get_page_content($url)
    {
        $client = new Client(['verify' => AppConfig::GUZZLE_VERIFY]);

        $req = $client->get($url);

        if ($req->getStatusCode() == 200){
            $content = $req->getBody()->getContents();

            $this->pages_content[$this->get_sheet_id_from_url($url)] = $content;

            return $content;
        }else{
            usleep(500000);
            return $this->get_page_content($url);
        }
    }

    public function get_pages()
    {
        $page_content = $this->get_page_content($this->report_url);

        $didom = new Document($page_content,false);

        $pages = $didom->find('#ddlTable')[0]->children();

        $data = [];

        foreach ($pages as $item){
            if ($item->getNode()->nodeName == 'option'){

                $title = trim($item->getNode()->textContent);

                if (in_array($title,$this->pages_to_get)){
                    $data[$title] = $item->getAttribute('value');
                }
            }
        }

        $this->pages = $data;
    }

    public function get_header_data()
    {
        $content = $this->pages_content[array_key_first($this->pages_content)];

        $didom = new Document($content,false);

        $rows = $didom->find('.text_holder');

        $data = [];

        foreach ($rows as $item){

            if ($item->getNode()->nodeName == 'div'){

                $boxes = $item->children();

                $tmp = [];

                foreach ($boxes as $box){
                    if ($box->getNode()->nodeName == 'div'){
                        $tmp[] = trim($box->getNode()->nodeValue);
                    }
                }

                if (isset($tmp[1])){
                    $data[str_replace(':','',$tmp[0])] = $tmp[1];
                }else{
                    $data['title'] = $tmp[0];
                }

            }
        }

        $this->page_header_content = $data;
    }

    public function get_all_pages()
    {
        foreach ($this->pages as $title => $sheet_id){
            $url = $this->get_page_url_by_sheet_id($sheet_id);

            $this->get_page_content($url);
        }
    }

    public function get_all_pages_data()
    {
        $data = [];
        foreach ($this->pages as $title => $sheet_id){
            if (in_array($title,$this->pages_has_general_table)){
                $data[$title] = $this->parse_general_tables($title);
            }elseif ($title == 'نظر حسابرس'){
                $data[$title] = $this->parse_auditor_opinion();
            }elseif ($title == 'ترازنامه'){
                $data[$title] = $this->parse_balance_sheet_table();
            }
        }
        $this->data = $data;

        $this->dd($data);
    }

    //parse pages data

    public function parse_general_tables($title)
    {
        $content = $this->get_page_content_by_name($title);

        $json = $this->get_json_data_from_html($content);

        $table_en_name = $this->get_en_table_name_by_title($title);

        $table_data = $this->get_en_table_data_from_json($json,$table_en_name);

        return $this->make_table_from_json($table_data);
    }

    public function parse_balance_sheet_table()
    {
        $data = $this->parse_general_tables('ترازنامه');

        $parsed_data = [];

        foreach ($data['data'] as $key => $value){

            if (isset($value[1]) && $value[1] != ''){
                $parsed_data[] = [
                    1 => $value[1],
                    2 => $value[2],
                    3 => $value[3],
                    4 => $value[4],
                ];
            }

            if (isset($value[5]) && $value[5] != ''){
                $parsed_data[] = [
                    1 => $value[5],
                    2 => $value[6],
                    3 => $value[7],
                    4 => $value[8],
                ];
            }
        }

        return [
            'data' => $parsed_data,
            'header' => $data['header']
        ];
    }

    public function parse_auditor_opinion()
    {
        $content = $this->get_page_content_by_name('نظر حسابرس');

        $didom = new Document($content);

        $table = $didom->find('#ctl00_cphBody_ucLetterAuditingV2_DataList1');

        return $table[0]->getNode()->textContent;
    }

    //logic parse codal json to table

    private function get_json_data_from_html($content){
        $tmp = explode('var datasource = ',$content)[1];

        $tmp = explode("<app-root></app-root>",$tmp)[0];

        $tmp = trim(str_replace('</script>','',$tmp));

        $tmp = substr($tmp,0,-1);

        return json_decode($tmp);
    }

    private function get_en_table_data_from_json($json,$table_name){

        $tables = $json->sheets[0]->tables;

        foreach ($tables as $table){
            if ($table->title_En == $table_name){
                return $table->cells;
            }
        }
        return null;
    }

    private function make_table_from_json($data){
        $table = [];

        $headers = [];

        foreach ($data as $item){
            if ($item->cellGroupName == 'Header'){
                if ($item->cssClass != 'dynamic_desc' && $item->isVisible){
                    $headers[$item->rowCode][$item->columnCode] = $item->value;
                }
            }else{
                if ($item->cssClass != 'dynamic_desc' && $item->isVisible){
                    $table[$item->rowCode][$item->columnCode] = $item->value;
                }
            }
        }

        return [
          'data' => $table,
          'header' => $headers
        ];
    }

    //helpers

    private function get_en_table_name_by_title($title){
        foreach ($this->pages_to_get_en as $key => $value){
            if ($key == $title){
                return $value;
            }
        }
    }

    private function get_sheet_id_from_url($url){
        return explode('sheetId=',parse_url($url)['query'])[1];
    }

    private function get_page_url_by_sheet_id($sheet_id){
        $parse_url = parse_url($this->report_url);
        $url_base = $parse_url['scheme'].'://'.$parse_url['host'].$parse_url['path'];
        $url_query = explode('sheetId=',$parse_url['query'])[0].'sheetId='.$sheet_id;
        return $url_base.'?'.$url_query;
    }

    private function get_page_content_by_name($title){
        return $this->pages_content[$this->pages[$title]];
    }

    public function dd($val)
    {
        echo '<pre>';

        var_dump($val);

        die();
    }
}