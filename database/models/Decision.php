<?php
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 8/1/21
 * Time: 19:49
 */

class Decision extends Model
{
    protected $table = AppConfig::TABLE_PREFIX.'report_decision';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function Report()
    {
        $this->belongsTo(Report::class);
    }

    public function DecisionData()
    {
        $this->hasMany(DecisionData::class);
    }
}