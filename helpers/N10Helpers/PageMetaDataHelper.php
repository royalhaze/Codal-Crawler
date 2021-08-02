<?php

use DiDom\Document;
use GuzzleHttp\Client;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 8/1/21
 * Time: 18:12
 */

class PageMetaDataHelper
{
    public $report_main_url,$report_id;

    private $pages_to_get = [
        'صورت سود و زیان',
        'نظر حسابرس',
        'صورت سود و زیان جامع',
        'صورت وضعیت مالی',
        'صورت جریان های نقدی',
        'جریان وجوه نقد',
        'ترازنامه'
    ];

    private $pages,$pages_content,$first_page_content,$page_header_content;

    public function __construct($url,$report_id)
    {
        $this->validate_url($url);

        $this->parseURL($url);

        $this->report_id = $report_id;

        $this->get_pages_title_and_url($url);

        $this->get_header_data();

        $this->get_all_pages();

        return $this;
    }

    private function validate_url($url){
        if (count(explode('sheetId=',$url)) == 2){
            throw new \Exception('آدرس وارد شده اشتباه است');
        }
    }

    public function get_data()
    {
        $parse_data = new ParsePagesData($this->pages,$this->pages_content);

        return new StorePagesData($this->page_header_content,$parse_data->parse_data_from_pages(),$this->report_id);
    }

    //guzzle & pages
    private function get_page($url,$store = true)
    {
        $client = new Client(['verify' => AppConfig::GUZZLE_VERIFY]);

        $req = $client->get($url);

        if ($req->getStatusCode() == 200){
            $content = $req->getBody()->getContents();

            if ($store){
                $this->pages_content[$this->get_sheet_id_from_url($url)] = $content;
            }else{
                $this->first_page_content = $content;
            }

            return $content;
        }else{
            usleep(500000);
            return $this->get_page($url);
        }
    }

    private function get_pages_title_and_url($url){
        $page_content = $this->get_page($url,false);

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

    private function get_header_data()
    {
        $content = $this->first_page_content;

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

    private function get_all_pages()
    {
        foreach ($this->pages as $title => $sheet_id){
            $url = $this->get_page_url_by_sheet_id($sheet_id);

            $this->get_page($url);
        }
    }

    //url helpers
    private function parseURL($url)
    {
        $explode = explode('&sheetId=',$url);

        $this->report_main_url = (count($explode) == 2)?$explode[0].'&sheetId=':$url.'&sheetId=';
    }

    private function get_page_url_by_sheet_id($sheet_id){
        return $sheet_id = $this->report_main_url.$sheet_id;
    }

    private function get_sheet_id_from_url($url){
        return explode('sheetId=',parse_url($url)['query'])[1];
    }
}