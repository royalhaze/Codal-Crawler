<?php
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 15:24
 */

class AppConfig
{
    const TABLE_PREFIX = 'cdt_';
    const SYMBOLS_TABLE_NAME = 'symbols_copy';
    const SYMBOL_NAME_COLUMN_NAME = 'symbol';
    const SYMBOL_ID_COLUMN_NAME = 'symbol_id';

    public static function CronJobLetterCodeFilter()
    {
        return [

        ];
    }

    public static function DefaultSearchParams()
    {
        return [
            'Audited' => 'true',
            'NotAudited' => 'true',
            'Childs' => 'true',
            'Publisher' => 'false',
            'Mains' => 'true',
            'Consolidatable' => 'true',
            'IsNotAudited' => 'false',
            'NotConsolidatable' => 'true',
            'AuditorRef' => -1,
            'Category' => -1,
            'CompanyState' => -1,
            'CompanyType' => -1,
            'Length' => -1,
            'LetterType' => -1,
            'TracingNo' => -1,
            'search' => 'true'
        ];
    }

    public static function DatabaseConfig()
    {
        return [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'codal',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock'
        ];
    }
}