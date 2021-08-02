<?php
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 8/1/21
 * Time: 19:50
 */

class DecisionData extends Model
{
    protected $table = AppConfig::TABLE_PREFIX.'report_decision_data';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function Decision()
    {
        $this->belongsTo(Decision::class);
    }
}