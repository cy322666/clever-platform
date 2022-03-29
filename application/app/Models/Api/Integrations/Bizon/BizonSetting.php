<?php

namespace App\Models\Api\Integrations\Bizon;

use App\Models\Api\Core\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BizonSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'pipeline_id',
        'status_id_cold',
        'status_id_soft',
        'status_id_hot',
        'responsible_user_id',
        'tag',
        'tag_cold',
        'tag_soft',
        'tag_hot',
        'strategy',
    ];

    protected $table = 'bizon_settings';

    public function account(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
}