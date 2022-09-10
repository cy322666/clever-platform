<?php


namespace App\Models\amoCRM;

use App\Models\Account;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class Pipeline extends Model
{
    protected $fillable = [
        'account_id',
        'is_main',
        'name',
        'pipeline_id',
    ];

    protected $table = 'amocrm_pipelines';

    public $timestamps = false;

    public function account()
    {
        return $this->hasOne(Account::class);
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }
}