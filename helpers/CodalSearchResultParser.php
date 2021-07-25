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
        $parsed_code = [];

        foreach ($code as $item){
            $parsed_code[] = $this->convertEnToFaNumber($item);
        }

        $this->letterCodeFilter = $parsed_code;

        return $this;
    }


    private function convertEnToFaNumber($string){
        $en_num = array('0','1','2','3','4','5','6','7','8','9');
        $fa_num = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
        return str_replace($en_num, $fa_num, $string);
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
        for ($i = count($this->letters); $i >= 1 ; $i--){

            $item = $this->letters[$i-1];

            if (Report::where('tracking_no',(int) $item->TracingNo)->count() == 0){
                Report::store_from_search_result($item);
            }
        }

//        foreach ($this->letters as $item){
//            if (Report::where('tracking_no',(int) $item->TracingNo)->count() == 0){
//                Report::store_from_search_result($item);
//            }
//        }

        return true;
    }
}