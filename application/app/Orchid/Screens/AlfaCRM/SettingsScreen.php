<?php

namespace App\Orchid\Screens\AlfaCRM;

use App\Models\amoCRM\Field;
use App\Models\amoCRM\Pipeline;
use App\Models\Feedback;
use App\Models\User;
use App\Orchid\Layouts\AlfaCRM\Settings\FieldsAlfaCRM;
use App\Orchid\Layouts\AlfaCRM\Settings\Info;
use App\Orchid\Layouts\AlfaCRM\Settings\Stages;
use App\Orchid\Layouts\AlfaCRM\Settings\Statuses;
use App\Services\AlfaCRM\Models\Branch;
use App\Services\AlfaCRM\Models\Customer;
use App\Services\AlfaCRM\Models\Source;
use App\Services\AlfaCRM\Models\Status;
use App\Services\amoCRM\Client as AmoApi;
use App\Services\Telegram\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use App\Services\AlfaCRM\Client as AlfaApi;
use Ramsey\Uuid\Uuid;

class SettingsScreen extends Screen
{
    public $setting;
    public $account;
    public $amoAccount;

    public AmoApi $amoApi;
    public $alfaApi;

    public $whStatusCame;
    public $whStatusOmission;
    public $whStatusRecord;

    public $fields;
    public $fieldsAlfaCRM;
    public $fieldsBranch;

    public function query(): iterable
    {
        $account = Auth::user()->alfaAccount();

        $setting = Auth::user()->alfaSetting;

        $amoAccount = Auth::user()->amoAccount();

        $this->alfaApi = (new \App\Services\AlfaCRM\Client($account));
        $this->amoApi  = (new amoApi($amoAccount));

        if ($setting->webhooks->count() == 0) {

            $setting->createWebhooks();
        }

        $this->amoApi->init();

        if ($this->amoApi->auth == false) {

            Alert::error('Ошибка подключения amoCRM');
        }

        return [
            'statuses' => $amoAccount
                ->amoStatuses()
                ->where('name', '!=', 'Неразобранное')
                ->orderBy('id')
                ->get(),

            'stages' => $account
                ->alfaStatuses()
                ->get(),

            'whStatusCame' => Auth::user()
                ->webhooks()
                ->where('app_id', 1)
                ->where('type', 'status_came')
                ->first(),

            'whStatusOmission' => Auth::user()
                ->webhooks()
                ->where('type', 'status_omission')
                ->where('user_id', Auth::user()->id)
                ->first(),

            'whStatusRecord' => Auth::user()
                ->webhooks()
                ->where('app_id', 1)
                ->where('type', 'status_record')
                ->first(),

            'account'    => $account,
            'setting'    => $setting,
            'amoAccount' => $amoAccount,

            'fieldsBranch' =>
                $amoAccount->fields(Field::class)
                    ->where('field_type', 4)
                    ->orWhere('field_type', 1)
                    ->pluck('name', 'id')
                    ->unique(),

            'fields' => json_decode($setting->fields),

            'fieldsAlfaCRM' => $account->fields(\App\Models\AlfaCRM\Field::class)->get(),
            'fieldsAmoCRM'  => $amoAccount->fields(Field::class)->get(),
        ];
    }

    public function name(): ?string
    {
        return 'Настройки';
    }

    public function feedbackSave(Request $request)
    {
        (new Client())->send('Фидбек из кабинета '.Auth::user()->email.' | сообщение : '.$request->message);

        Feedback::query()->create([
            'user' => Auth::user()->email,
            'message' => $request->message,
            'type' => 'feedback',
        ]);

        Toast::success('Сообщение отправлено');
    }

    public function questionSave(Request $request)
    {
        (new Client())->send('Вопрос из кабинета '.Auth::user()->email.' | контакты '.$request->contacts.' сообщение : '.$request->message);

        Feedback::query()->create([
            'user' => Auth::user()->email,
            'message'  => $request->message,
            'contacts' => $request->contacts,
            'type' => 'question',
        ]);

        Toast::success('Сообщение отправлено');
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Задать вопрос')
                ->method('questionSave')
                ->modal('question')
                ->icon('globe-alt'),

            Link::make('Инструкция')
                ->icon('docs')
                ->href('https://www.youtube.com/watch?v=Jg-9-eqAYzM&t=1s'),

            ModalToggle::make('Обратная связь')
                ->method('feedbackSave')
                ->modal('feedback')
                ->icon('social-github'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::block(Info::class)
                ->title('Статус интеграции')
                ->description('Проверьте все настройки'),

            Layout::block(Layout::rows([
                Group::make([
                    Button::make('Поля amoCRM')
                        ->method('updateFieldsAmo')
                        ->type(Color::DEFAULT()),

                    Button::make('Статусы amoCRM')
                        ->method('updateStatusesAmo')
                        ->type(Color::DEFAULT()),

                    Button::make('Поля AlfaCRM')
                        ->method('updateFieldsAlfa')
                        ->type(Color::DEFAULT()),

                    Button::make('Общее AlfaCRM')
                        ->method('updateSystemAlfa')
                        ->type(Color::DEFAULT()),

                ])->autoWidth()
            ]))
                ->title('Обновить данные')
                ->description('Если нужно обновить данные систем, то нажмите нужную кнопку'),

            Layout::block(Layout::rows([
                Group::make([
                    Button::make('Сохранить')
                        ->method('save')
                        ->type(Color::INFO())->horizontal(),

//                    Button::make('Диагностика')
//                        ->method('diagnostic')
//                        ->type(Color::DARK())->horizontal(),//TODO проверка авторизаций и настроек

                    Button::make('Сбросить')
                        ->confirm('Настройки интеграции будут сброшены')
                        ->method('resetSetting')
                        ->type(Color::WARNING())->horizontal()
                ])->autoWidth(),
            ])),

            Layout::tabs([
                'Поля' => Layout::columns([
                    $this->prepareFields(),
                    FieldsAlfaCRM::class,
                ]),
                'Статусы' => Layout::columns([
                    Layout::rows([
                        Select::make('setting.branch_id')
                            ->title('Поле для соотношения филиалов')
                            ->popover('Названия филиалов в поле должны совпадать с теми, что в АльфаСРМ. Поле должно быть списком. Если филиал один, то оставьте пустым')
                            ->options($this->fieldsBranch)
                            ->empty('Не выбрано'),

                        Input::make('setting.status_record_1')
                            ->title('Этап записи')
//                            ->required()
                            ->help('Этап на котором клиента записывают на пробное'),

                        Input::make('setting.status_came_1')
                            ->title('Этап посещения')
//                            ->required()
                            ->help('Этап на который сделка передвигается при посещении пробного'),

                        Input::make('setting.status_omission_1')
                            ->title('Этап пропуска')
//                            ->required()
                            ->help('Этап на который сделка передвигается при отмене пробного'),
                    ]),
                    Statuses::class,
                ]),
                'Этапы' => Layout::columns([
                    Layout::rows([

                        Label::make('setting.stage_info')
                            ->title('Этот раздел используется, если нужно работать с лидами'),

                        Input::make('setting.stage_record_1')
                            ->title('Этап записи')
                            ->help('Этап на котором клиента записывают на пробное'),

                        Input::make('setting.stage_came_1')
                            ->title('Этап посещения')
                            ->help('Этап на который сделка передвигается при посещении пробного'),

                        Input::make('setting.stage_omission_1')
                            ->title('Этап пропуска')
                            ->help('Этап на который сделка передвигается при отмене пробного'),
                    ]),
                    Stages::class,
                ]),
                'Вебхуки' => [
                    Layout::rows([
                        Label::make('wh1')
                            ->title('Хук о посещении пробного')
                            ->value(URL::route($this->whStatusCame->path, [
                                'webhook' => $this->whStatusCame->uuid,
                            ])),

                        Label::make('wh2')
                            ->title('Хук о пропуске пробного')
                            ->value(URL::route($this->whStatusOmission->path, [
                                'webhook' => $this->whStatusOmission->uuid,
                            ])),
                    ]),
                ],
            ]),
            Layout::modal('feedback', Layout::rows([
                TextArea::make('message')
                    ->title('Сообщение')
                    ->help('Вы точно будете услышаны!')
                    ->required(),
            ]))
                ->closeButton('Закрыть')
                ->applyButton('Отправить')
                ->title('Оставьте обратную связь'),

            Layout::modal('question', Layout::rows([
                Input::make('contacts')
                    ->title('Ваши контакты')
                    ->required()
                    ->help('Оставьте свои контакты для связи'),

                TextArea::make('message')
                    ->title('Сообщение')
                    ->help('Например запрос на доработку или внедрение')
                    ->required(),
            ]))
                ->closeButton('Закрыть')
                ->applyButton('Отправить')
                ->title('Задайте свой вопрос'),
        ];
    }

    public function resetSetting()
    {
        try {
            $fieldsRaw = json_decode($this->account->fields(Field::class)->get());

            $fields = [];

            foreach ($fieldsRaw as $code => $value) {

                $fields[$code] = null;
            }

            $this->setting->fields = json_encode($fields);

            $this->setting->status_came_1 = null;
            $this->setting->status_record_1 = null;
            $this->setting->status_omission_1 = null;

            $this->setting->stage_came_1 = null;
            $this->setting->stage_record_1 = null;
            $this->setting->stage_omission_1 = null;

            $this->setting->branch_id = null;
            $this->setting->active = false;
            $this->setting->save();

            $this->amoAccount->fields(Field::class)->delete();
            $this->amoAccount->amoStatuses()->delete();
            $this->amoAccount->amoPipelines()->delete();

            $this->account->fields(\App\Models\AlfaCRM\Field::class)->delete();
            $this->account->alfaBranches()->delete();
            $this->account->alfaStatuses()->delete();
            $this->account->alfaSources()->delete();

            Toast::info('Настройки успешно сброшены');

        } catch (\Exception $exception) {

            Log::error(__METHOD__.' : '.$exception->getMessage());

            Alert::error($exception->getMessage().' '.$exception->getLine());
        }
    }

    private function prepareFields(): \Orchid\Screen\Layouts\Rows
    {
        $fields = [];

        //TODO поле для ссылки на клиента в альфе

        foreach ($this->fieldsAlfaCRM as $field) {

            $fields[] = Input::make('fields.'.$field->code)
                ->title($field->name)
//                ->required($field->required)
                ->value($this->fields->{$field->code} ?? null);
        }

        return Layout::rows($fields);
    }

    public function save(Request $request)
    {
        try {
            $this->amoApi = (new \App\Services\amoCRM\Client(Auth::user()->amoAccount()));
            $this->amoApi->init();

            if ($this->amoApi->auth == false) {

                Log::error(__METHOD__.' '.Auth::user()->email, [$this->amoApi]);

                Alert::error('Подключите amoCRM к платформе!');

                return;
            }

            $this->account->code      = $request->account['code'];
            $this->account->client_id = $request->account['client_id'];
            $this->account->subdomain = $request->account['subdomain'];
            $this->account->save();

            $this->setting->fill([
                'active'    => $request->setting['active'] ?? false,
                'work_lead' => $request->setting['work_lead'],
                'branch_id' => $request->setting['branch_id'],
                'stage_came_1'      => $request->setting['stage_came_1'],
                'stage_record_1'    => $request->setting['stage_record_1'],
                'stage_omission_1'  => $request->setting['stage_omission_1'],
                'status_came_1'     => $request->setting['status_came_1'],
                'status_record_1'   => $request->setting['status_record_1'],
                'status_omission_1' => $request->setting['status_omission_1'],
            ]);

            $this->setting->fields = $request->fields;
            $this->setting->save();

            if (!$this->whStatusRecord) {

                $wh = $this->setting->webhooks()->create([
                    'user_id'  => Auth::user()->id,
                    'app_name' => 'alfacrm',
                    'app_id'   => 1,
                    'active'   => true,
                    'path'     => 'alfacrm.record',
                    'type'     => 'status_record',
                    'platform' => 'amocrm',
                    'uuid'     => Uuid::uuid4(),
                ]);

                if ($this->amoApi->auth === true) {

                    $response = $this->amoApi->service
                        ->webhooks()
                        ->subscribe(URL::route($wh->path, [
                            'webhook' => $wh->uuid,
                        ]), [
                            'status_lead', 'add_lead'
                        ]);

                    if ($response !== true) {

                        Log::error(__METHOD__.' '.Auth::user()->email.' Не удалось создать вебхук в amoCRM');

                        Alert::error('Не удалось создать вебхук в amoCRM');
                    }
                }
            }

            try {
                $this->alfaApi = (new \App\Services\AlfaCRM\Client($this->account->refresh()));
                $this->alfaApi->init();

                $this->account->active = true;
                $this->account->save();

                Toast::success('Успешно сохранено');

            } catch (\Throwable $exception) {

                Alert::error('Ошибка авторизации AlfaCRM!');

                Log::error(__METHOD__ . ' ' . Auth::user()->email . ' auth AlfaCRM error ', [$this->alfaApi]);

                return;
            }

        } catch (\Exception $exception) {

            $this->setting->active = false;

            Log::error(__METHOD__.' '.Auth::user()->email.' '.$exception->getMessage().' '.$exception->getFile().' '.$exception->getLine());
            Log::error(__METHOD__.' '.Auth::user()->email, [$this->amoApi]);

            Alert::error('Произошла ошибка сохранения');

        } finally {

            $this->setting->save();
            $this->account->save();
        }
    }

    public function updateFieldsAmo()
    {
        try {
            $account = Auth::user()->amoAccount();

            $this->amoApi = (new AmoApi($account));
            $this->amoApi->init();

            Field::updateFields($this->amoApi, $account);

            Toast::success('Успешно обновлено');

        } catch (\Exception $exception) {

            $this->setting->active = false;
            $this->setting->save();

            Log::error(__METHOD__.' : '.Auth::user()->email.' '.$exception->getMessage());

            Alert::error('Ошибка обновления');
        }
    }

    public function updateStatusesAmo()
    {
        try {
            $account = Auth::user()->amoAccount();

            $this->amoApi = (new AmoApi($account));
            $this->amoApi->init();

            Pipeline::updateStatuses($this->amoApi, $account);

            Toast::success('Успешно обновлено');

        } catch (\Exception $exception) {

            $this->setting->active = false;
            $this->setting->save();

            Log::error(__METHOD__.' : '.Auth::user()->email.' '.$exception->getMessage());

            Alert::error('Ошибка обновления');
        }
    }

    public function updateFieldsAlfa()
    {
        try {
            $this->alfaApi->init();

            $this->account->fields(\App\Models\AlfaCRM\Field::class)->delete();

            foreach((new Customer($this->alfaApi))->first() as $fieldName => $fieldValue) {

                if (!in_array($fieldName, \App\Models\AlfaCRM\Customer::$ignoreFields)) {

                    $this->account
                        ->fields(\App\Models\AlfaCRM\Field::class)
                        ->create([
                            'code'   => $fieldName,
                            'name'   => \App\Models\AlfaCRM\Customer::matchField($fieldName),
                            'entity' => 1,
                            'required' => in_array($fieldName, \App\Models\AlfaCRM\Customer::$required),
                        ]);
                }
            }

            Toast::success('Успешно обновлено');

        } catch (\Exception $exception) {

            $this->setting->active = false;
            $this->setting->save();

            Log::error(__METHOD__.' : '.Auth::user()->email.' '.$exception->getMessage());

            Alert::error('Ошибка обновления');
        }
    }

    public function updateSystemAlfa()
    {
        try {
            $this->alfaApi->init();

            $this->account->alfaBranches()->delete();
            $this->account->alfaSources()->delete();
            $this->account->alfaStatuses()->delete();

            foreach((new Branch($this->alfaApi))->all() as $branch) {

                $this->account
                    ->alfaBranches()
                    ->create([
                        'branch_id' => $branch->id,
                        'name'      => $branch->name,
                        'is_active' => $branch->is_active,
                        'weight'    => $branch->weight,
                        'subject_ids' => json_encode($branch->subject_ids),
                    ]);
            }

            foreach((new Status($this->alfaApi))->all() as $status) {

                $this->account
                    ->alfaStatuses()
                    ->create([
                        'name'       => $status->name,
                        'is_enabled' => $status->is_enabled,
                        'status_id'  => $status->id,
                    ]);
            }

            foreach((new Source($this->alfaApi))->all() as $source) {

                $this->account
                    ->alfaSources()
                    ->create([
                        'name'       => $source->name,
                        'is_enabled' => $source->is_enabled,
                        'code'       => $source->code,
                        'source_id'  => $source->id,
                    ]);
            }

            Toast::success('Успешно обновлено');

        } catch (\Exception $exception) {

            $this->setting->active = false;
            $this->setting->save();

            Log::error(__METHOD__.' : '.Auth::user()->email.' '.$exception->getMessage());

            Alert::error('Ошибка обновления');
        }
    }
}
