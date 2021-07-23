<?php
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 18:44
 */

class CodalSearchResultParser
{
    private $letters,$letterCodeFilter = [];

    public function __construct(CodalSearch $codalSearch)
    {
        $this->letters = $codalSearch->result->Letters;
    }

    public function addLetterCodeFilter(array $code = [])
    {
        $this->letterCodeFilter = $code;

        return $this;
    }

    public function store()
    {
        if (count($this->letterCodeFilter) != 0){
            $tmp = [];

            foreach ($this->letters as $item){
                foreach ($this->letterCodeFilter as $rule){
                    if ($item->LetterCode == $rule){
                        $tmp[] = $item;
                    }
                }
            }

            $this->letters = $tmp;
        }

        return $this->store_result();
    }

    private function store_result(){
        foreach ($this->letters as $item){
            if (Report::where('tracking_no',(int) $item->TracingNo)->count() == 0){
                Report::store_from_search_result($item);
            }
        }

        return true;
    }
}