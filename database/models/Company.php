<?php

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 18:18
 */

class Company extends Model
{
    protected $table = 'company';

    protected $guarded = ['id'];

    public function Report()
    {
        $this->hasMany(Report::class);
    }
}