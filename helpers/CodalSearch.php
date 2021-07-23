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
    private $searchParams;

    public $result;

    public function __construct(CodalSearchFilter $searchFilter = null)
    {
        $this->searchParams = ($searchFilter == null) ? new CodalSearchFilter() : $searchFilter;
    }

    public function search($page = 1)
    {
        $this->searchParams->params['PageNumber'] = $page;

        $client = new Client(['verify' => false]);

        $res = $client->get(CodalConst::SEARCH_API_URL, ['query' => $this->searchParams->params]);

        if ($res->getStatusCode() != 200) {
            throw new \Exception('Codal return with status ' . $res->getStatusCode());
        }

        $this->result = json_decode($res->getBody()->getContents());

        return $this;
    }

    public function get_result()
    {
        return new CodalSearchResultParser($this);
    }

    public static function get_companies()
    {
        $client = new Client(['verify' => false]);

        $res = $client->get(CodalConst::COMPANY_API_URL);

        if ($res->getStatusCode() != 200) {
            throw new \Exception('Codal return with status ' . $res->getStatusCode());
        }

        return self::store_companies(json_decode($res->getBody()->getContents()));
    }

    private static function store_companies($result)
    {
        foreach ($result as $key => $item) {
            if ($key<200){
                $db = Company::store_by_search_result($item);
                if (!$db) {
                    throw new \Exception();
                }
                unset($result[$key]);
            }else{
                break;
            }
        }

        if (count($result) != 0){
            $result = array_values($result);

            self::store_companies($result);
        }

        return true;
    }
}