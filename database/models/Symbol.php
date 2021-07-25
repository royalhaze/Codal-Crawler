<?php
require __DIR__.'/../bootstrap.php';
use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/25/21
 * Time: 20:18
 */

class Symbol extends Model
{
    protected $table = AppConfig::SYMBOLS_TABLE_NAME;

    protected $guarded = ['id'];

    public static function getSymbolIdBySymbol($symbol)
    {
        $db = Symbol::where(AppConfig::SYMBOL_NAME_COLUMN_NAME,$symbol);
        $symbol_id_column_name = AppConfig::SYMBOL_ID_COLUMN_NAME;
        if ($db->count() == 0){
            return null;
        }else{
            return $db->first()->$symbol_id_column_name;
        }
    }
}