<?php
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 8/1/21
 * Time: 19:44
 */

require __DIR__.'/../../bootstrap.php';
use Illuminate\Database\Capsule\Manager as Capsule;


$db = Capsule::schema();

$tables = ['report_decision','report_decision_data'];

$tables_prefix = AppConfig::TABLE_PREFIX;

foreach ($tables as $item){
    if ($db->hasTable($tables_prefix.$item)){
        $db->drop($tables_prefix.$item);
        echo $tables_prefix.$item.' table drop success'.PHP_EOL;
    }
}