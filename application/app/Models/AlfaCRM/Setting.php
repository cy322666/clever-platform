<?php

namespace App\Models\AlfaCRM;

use App\Models\Account;
use App\Models\amoCRM\Field;
use App\Models\Webhook;
use App\Services\AlfaCRM\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Ufee\Amo\Models\Contact;
use Ufee\Amo\Models\Lead;
use App\Services\AlfaCRM\Client as alfaApi;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'alfacrm_settings';

    protected $fillable = [
        'status_came_1',
        'status_came_2',
        'status_came_3',
        'status_record_1',
        'status_record_2',
        'status_record_3',
        'status_omission_1',
        'status_omission_2',
        'status_omission_3',

        'stage_record_1',
        'stage_came_1',
        'stage_omission_1',

        'active',
        'work_lead',

        'name',
        'source',
        'responsible',
        'legal_name',
        'dob',
        'note',
        'phone',
        'web',

        'branch_id',
        'account_id',
    ];

    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }

    public function checkStatus(string $action, int $statusId): bool
    {
        $action = 'status_'.$action;

        return match ($statusId) {

            $this->{$action.'_1'},
            $this->{$action.'_3'},
            $this->{$action.'_2'} => true,

            default => false,
        };
    }

    public static function getFieldBranch(Lead $lead, ?Contact $contact, Setting $setting): string
    {
        if ($setting->branch_id) {

            //TODO может и не найти тут эксепшен и уведомление
            $fieldBranch = \App\Models\amoCRM\Field::find($setting->branch_id);
        }

        if (!empty($fieldBranch)) {

            if ($fieldBranch->field_id) {

                $entity = $fieldBranch->entity == 1 ? $contact : $lead;

                $branchName = $entity->cf($fieldBranch->name)->getValue();
            }
        }

        return $branchName ?? false;
    }

    /*
        $fields - json в поле
        $code - поле из альфы
        $fieldName - название поля амо в бд (в сущности)
        $fieldValues - массив со значениями для клиента в АльфаСРМ
    */
    public function getFieldValues(Lead $lead, ?Contact $contact, Account $account, Account $alfaAccount): array
    {
        foreach (json_decode($this->fields) as $code => $fieldName) {

            if ($fieldName !== null) {

                $amoField = $account->fields(Field::class)
                    ->where('name', $fieldName)
                    ->first();

                $entity = $amoField->entity == 1 ? $contact : $lead;

                if ($amoField->field_id) {

                    $fieldValue = $entity->cf($amoField->name)->getValue();
                } else
                    $fieldValue = $entity->{$amoField->code};

                //исключительные поля
                if ($code == 'lead_source_id' && $fieldValue) {

                    $fieldValue = $alfaAccount->alfaSources()
//                        ->where('name', 'like', '%'.$fieldValue.'%')
                        ->where('name', $fieldValue)
                        ->first()
                            ?->source_id;
                }

                $fieldValues[$code] = $fieldValue;
            }
        }

        return $fieldValues ?? [];
    }

    public static function getBranchId(Lead $lead, Contact $contact, Account $account, Setting $setting)
    {
        $branchId = $account->alfaBranches()
            ->orderBy('branch_id')
            ->first()
            ->branch_id;

        $branchValue = self::getFieldBranch($lead, $contact, $setting);
        Log::info(__METHOD__. ' name 2 '.$branchValue);
        if ($branchValue) {

            foreach ($account->alfaBranches as $branch) {

            Log::info(__METHOD__.' '.$branchValue.' - '.$branch->name);

                if (trim(mb_strtolower($branch->name)) == trim(mb_strtolower($branchValue))) {

                    $branchId = $branch->branch_id;

                    break;
                }
            }
        }
        return $branchId;
    }

    public static function customerUpdateOrCreate(array $fieldValues, alfaApi $alfaApi)
    {
        $customers = (new Customer($alfaApi))->search($fieldValues['phone']);

        if ($customers->total == 0) {
            $customer = (new Customer($alfaApi))->create($fieldValues);
        } else {
            $customer = $customers->items[0];

            $fieldValues['branch_ids'] = array_merge($customer->branch_ids, [$fieldValues['branch_id']]);
        }

        (new Customer($alfaApi))->update($customer->id, $fieldValues);

        return $customer;
    }

    public function createWebhooks()
    {
        $this->webhooks()->create([
            'user_id'  => Auth::user()->id,
            'app_name' => 'alfacrm',
            'app_id'   => 1,
            'active'   => true,
            'path'     => 'alfacrm.came',
            'type'     => 'status_came',
            'platform' => 'alfacrm',
            'uuid'     => Uuid::uuid4(),
        ]);

        $this->webhooks()->create([
            'user_id'  => Auth::user()->id,
            'app_name' => 'alfacrm',
            'app_id'   => 1,
            'active'   => true,
            'path'     => 'alfacrm.omission',
            'type'     => 'status_omission',
            'platform' => 'alfacrm',
            'uuid'     => Uuid::uuid4(),
        ]);
    }
}
