<?php
require __DIR__.'/../bootstrap.php';
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 16:37
 */

$db = Capsule::schema();

$tables = ['reports','report_data','company'];

foreach ($tables as $item){
    if ($db->hasTable($item)){
        $db->drop($item);
        echo $item.' table drop success'.PHP_EOL;
    }
}
