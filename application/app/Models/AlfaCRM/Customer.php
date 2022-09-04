<?php

namespace App\Models\AlfaCRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'alfacrm_customers';

    protected $fillable = [
        'account_id',
    ];

    public static array $ignoreFields = [
        'id',
        'branch_ids',
        'teacher_ids',
        'is_study',
        'color',
        'study_status_id',
        'lead_status_id',
        'lead_reject_id',
        'legal_type',
        'balance_bonus',
        'balance_base',
        'paid_count',
        'next_lesson_date',
        'paid_till',
        'paid_lesson_date',
        'last_attend_date',
        'company_id',
    ];

    public static function matchField(string $fieldName)
    {
        return match ($fieldName) {
            'name' => 'Полное имя',
            'lead_source_id' => 'Источник',
            'assigned_id' => 'Ответственный',
            'legal_name'  => 'Имя заказчика',
            'dob'     => 'Дата рождения',
//            'balance' => 'Текущий остаток, деньги',
//            'paid_lesson_count' => 'Текущий остаток, занятия',
            'phone' => 'Телефон',
            'email' => 'Почта',
            'web'   => 'Сайт',
            'addr'  => 'Адрес',
            'note'  => 'Примечание',
            default => $fieldName,
        };
    }

//study_status_id

//статус обучения (StudyStatus)

//этап воронки продаж (LeadStatus)

}
