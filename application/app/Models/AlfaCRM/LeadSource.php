<?php

namespace App\Models\AlfaCRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    use HasFactory;

    protected $table = 'alfacrm_lead_sources';

    protected $fillable = [
        'account_id',
        'code',
        'name',
        'is_enabled',
        'source_id',
    ];
}
