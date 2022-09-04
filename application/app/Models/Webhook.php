<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psy\Util\Str;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'app_id',
        'active',
        'path',
        'type',
        'platform',
        'uuid',
        'user_id',
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function settings(string $class)
    {
        return $this->belongsTo($class, 'setting_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
