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

if ($db->hasTable('reports') || $db->hasTable('report_data') || $db->hasTable('company')){
    echo "Please drop table's first !!!".PHP_EOL;
    die();
}


//create reports table
Capsule::schema()->create('reports',function ($table){
    $table->increments('id');
    $table->integer('company_id');
    $table->string('title');
    $table->integer('tracking_no');
    $table->string('letter_code')->nullable();
    $table->string('pdf_url')->nullable();
    $table->string('excel_url')->nullable();
    $table->string('xbrl_url')->nullable();
    $table->boolean('has_attachment')->default(0);
    $table->boolean('has_super_vision')->default(0);
    $table->boolean('under_super_vision')->default(0);
    $table->timestamp('publish_time');
    $table->timestamp('crawl_time');
});

echo 'reports table created successfully'.PHP_EOL;

//create company table
Capsule::schema()->create('company',function ($table){
    $table->increments('id');
    $table->string('symbol',100);
    $table->string('name',256);
    $table->integer('codal_id')->nullable();
    $table->integer('codal_t')->nullable();
    $table->integer('codal_st')->nullable();
});

echo 'company table created successfully'.PHP_EOL;

//create report data table
Capsule::schema()->create('report_data',function ($table){
    $table->increments('id');
    $table->integer('report_id');
    $table->string('title');
    $table->text('value');
});

echo 'report_data table created successfully'.PHP_EOL;
