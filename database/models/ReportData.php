<?php

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 18:18
 */

class ReportData extends Model
{
    protected $table = 'report_data';

    protected $guarded = ['id'];

    public function Report()
    {
        $this->belongsTo(Report::class);
    }
}