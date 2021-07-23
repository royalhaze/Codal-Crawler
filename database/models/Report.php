<?php

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 16:35
 */

class Report extends Model
{
    protected $table = 'reports';

    protected $guarded = ['id'];

    public function Company()
    {
        $this->belongsTo(Company::class);
    }

    public function ReportData()
    {
        $this->hasMany(ReportData::class);
    }
}