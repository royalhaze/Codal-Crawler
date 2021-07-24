<?php

use Carbon\Carbon;

include 'vendor/autoload.php';

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 22:03
 */

//  1-first run composer install and set db config in /config/AppConfig.php

/*  2- then run
php database/migrations/create_tables.php
command to create tables
*/

//  3-now you must store companies by this line

CodalSearch::get_companies();

//and get Codal Reports

$crawl = new CodalSearch();

$crawl->search()->get_result()->store();


//for add filter to store

$crawl = new CodalSearch();

$crawl->search(1)->get_result()->addLetterCodeFilter(['ن-۱۰'])->store();


//even can set filter for search like this

$filter = new CodalSearchFilter();

$filter->Publisher(true);

$filter->Audited(true);

$crawl = new CodalSearch($filter);

$crawl->search()->get_result()->store();