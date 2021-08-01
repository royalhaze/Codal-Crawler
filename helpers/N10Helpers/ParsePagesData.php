<?php

use DiDom\Document;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 8/1/21
 * Time: 19:09
 */

class ParsePagesData
{
    private $pages , $pages_content;

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

    public function __construct($pages,$pages_content)
    {
        $this->pages_content = $pages_content;

        $this->pages = $pages;
    }

    public function parse_data_from_pages()
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
        return $data;
    }


    //parse pages data

    private function parse_general_tables($title)
    {
        $content = $this->get_page_content_by_name($title);

        $json = $this->get_json_data_from_html($content);

        $table_en_name = $this->get_en_table_name_by_title($title);

        $table_data = $this->get_en_table_data_from_json($json,$table_en_name);

        return $this->make_table_from_json($table_data);
    }

    private function parse_balance_sheet_table()
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

    private function parse_auditor_opinion()
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

    //other helpers

    private function get_page_content_by_name($title){
        return $this->pages_content[$this->pages[$title]];
    }


    private function get_en_table_name_by_title($title){
        foreach ($this->pages_to_get_en as $key => $value){
            if ($key == $title){
                return $value;
            }
        }
    }
}