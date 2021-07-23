<?php

use GuzzleHttp\Client;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 14:23
 */

class CodalSearch
{
    private $searchParams,$result;

    public function __construct(CodalSearchFilter $searchFilter = null)
    {
        $this->searchParams = ($searchFilter == null)?new CodalSearchFilter():$searchFilter;
    }

    public function search($page = 1)
    {
        $this->searchParams->params['PageNumber'] = $page;

        $client = new Client(['verify' => false]);

        $res = $client->get(CodalConst::SEARCH_API_URL,['query' => $this->searchParams->params]);

        if ($res->getStatusCode() != 200){
            throw new \Exception('Codal return with status '.$res->getStatusCode());
        }

        $this->result = json_decode($res->getBody()->getContents());

        return $this;
    }

    public function get_result()
    {
        return $this->result;
    }

    public function filter_by_()
    {

    }
}