<?php
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 16:27
 */
require __DIR__.'/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();

$capsule->addConnection(AppConfig::DatabaseConfig());

$capsule->setAsGlobal();

$capsule->bootEloquent();