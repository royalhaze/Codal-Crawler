<?php
require __DIR__.'/../bootstrap.php';
use Illuminate\Database\Capsule\Manager as Capsule;
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/28/21
 * Time: 00:07
 */

$db = Capsule::schema();

if ($db->hasTable('reports') || $db->hasTable('report_data') || $db->hasTable('company')){
    echo "Please drop table's first !!!".PHP_EOL;
    die();
}

$tables_prefix = AppConfig::TABLE_PREFIX;

//create reports table
Capsule::schema()->create($tables_prefix.'report_decision',function ($table){
    $table->increments('id');
    $table->integer('report_id')->unsigned();
    $table->integer('company_id')->unsigned();
    $table->string('symbol_id',20)->nullable();
    $table->string('symbol');
    $table->string('year_leading_to');
    $table->integer('registered_fund');
    $table->integer('unregistered_fund');
    $table->string('publisher_status');
});

echo 'reports table created successfully'.PHP_EOL;

//create report data table
Capsule::schema()->create($tables_prefix.'report_decision_data',function ($table){
    $table->increments('id');
    $table->integer('report_id')->unsigned();
    $table->string('for_page',64);
    $table->string('title',128);
    $table->integer('value');
    $table->string('date_leading_to');
    $table->integer('change');
    $table->boolean('is_for_this_year');
});

echo 'report_data table created successfully'.PHP_EOL;
