<?php
require __DIR__.'/../../bootstrap.php';
use Illuminate\Database\Capsule\Manager as Capsule;
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/28/21
 * Time: 00:07
 */
$tables_prefix = AppConfig::TABLE_PREFIX;
$db = Capsule::schema();

if ($db->hasTable($tables_prefix.'report_decision') || $db->hasTable('report_decision_data')){
    echo "Please drop table's first !!!".PHP_EOL;
    die();
}



//create reports table
Capsule::schema()->create($tables_prefix.'report_decision',function ($table){
    $table->increments('id');
    $table->integer('report_id')->unsigned()->nullable();
    $table->integer('company_id')->unsigned()->nullable();
    $table->string('symbol_id',20)->nullable()->nullable();
    $table->string('symbol')->nullable();
    $table->string('year_leading_to')->nullable();
    $table->integer('registered_fund')->nullable();
    $table->integer('unregistered_fund')->nullable();
    $table->string('publisher_status')->nullable();
});

echo 'report_decision table created successfully'.PHP_EOL;

//create report data table
Capsule::schema()->create($tables_prefix.'report_decision_data',function ($table){
    $table->increments('id');
    $table->integer('report_id')->unsigned()->nullable();
    $table->string('for_page',64)->nullable();
    $table->string('title',128)->nullable();
    $table->integer('value')->nullable();
    $table->string('date_leading_to')->nullable();
    $table->integer('change')->nullable();
    $table->integer('col_no')->nullable();
});

echo 'report_decision_data table created successfully'.PHP_EOL;
