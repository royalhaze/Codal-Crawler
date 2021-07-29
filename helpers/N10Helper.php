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

    private $report_url,$pages;

    private $pages_content = [];

    public $company_type;

    public $page_header_content = [];

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

    private function get_sheet_id_from_url($url){
        return explode('sheetId=',parse_url($url)['query'])[1];
    }

    //detect company type !?

    //parse pages data

    public function parse_profit_and_loss_table_data()
    {
        $content = $this->get_page_content_by_name('صورت سود و زیان');

        $json = $this->get_json_data_from_html($content);

        $table_data = $this->get_table_data_from_json($json,'صورت سود و زیان');

        return $this->make_table_from_json($table_data);
    }

    public function parse_general_profit_and_loss_table_data()
    {
        $content = $this->get_page_content_by_name('صورت سود و زیان جامع');

        $json = $this->get_json_data_from_html($content);

        $table_data = $this->get_table_data_from_json($json,'صورت سود و زیان جامع');

        return $this->make_table_from_json($table_data);
    }

    public function parse_financial_statements_table_data()
    {
        $content = $this->get_page_content_by_name('صورت وضعیت مالی');

        $json = $this->get_json_data_from_html($content);

        $table_data = $this->get_table_data_from_json($json,'صورت وضعیت مالی');

        return $this->make_table_from_json($table_data);
    }

    public function parse_cash_flow_statement_table_data()
    {
        $content = $this->get_page_content_by_name('صورت جریان های نقدی');

        $json = $this->get_json_data_from_html($content);

        $table_data = $this->get_Entable_data_from_json($json,'Cash Flow');

        return $this->make_table_from_json($table_data);
    }

    public function parse_cash_flow_table_data()
    {
        $content = $this->get_page_content_by_name('جریان وجوه نقد');

        $json = $this->get_json_data_from_html($content);

        $table_data = $this->get_Entable_data_from_json($json,'Cash Flow');

        return $this->make_table_from_json($table_data);
    }

    public function parse_balance_sheet_table_data()
    {
        $content = $this->get_page_content_by_name('ترازنامه');

        $json = $this->get_json_data_from_html($content);

        $table_data = $this->get_Entable_data_from_json($json,'Balance Sheet');

        $this->dd($this->make_table_from_json($table_data));
    }

    //logic parse codal json to table

    private function get_page_content_by_name($title){
        return $this->pages_content[$this->pages[$title]];
    }

    private function get_json_data_from_html($content){
        $tmp = explode('var datasource = ',$content)[1];

        $tmp = explode("<app-root></app-root>",$tmp)[0];

        $tmp = trim(str_replace('</script>','',$tmp));

        $tmp = substr($tmp,0,-1);

        return json_decode($tmp);
    }

    private function get_table_data_from_json($json,$table_name){

        $tables = $json->sheets[0]->tables;

        foreach ($tables as $table){
            if ($table->title_Fa == $table_name){
                return $table->cells;
            }
        }
        return null;
    }

    private function get_Entable_data_from_json($json,$table_name){

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

    public function dd($val)
    {
        echo '<pre>';

        var_dump($val);

        die();
    }
}