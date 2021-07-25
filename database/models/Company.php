<?php
require __DIR__.'/../bootstrap.php';
use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 18:18
 */

class Company extends Model
{
    protected $table = AppConfig::TABLE_PREFIX.'company';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function Report()
    {
        $this->hasMany(Report::class);
    }

    public static function getBySymbol($symbol)
    {
        return Company::where('symbol',$symbol)->firstOrFail();
    }

    public static function store_by_search_result($data)
    {
        $db = Company::updateOrCreate(
            ['symbol' => $data->sy , 'name' => $data->n],
            ['codal_id' => (int)$data->i,'codal_t' => (int)$data->t,'codal_st' =>(int) $data->st]
        );

        return ($db instanceof Company)?true:false;
    }
}