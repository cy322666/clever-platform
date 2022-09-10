<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\AlfaCRM\CameRequest;
use App\Http\Requests\Api\AlfaCRM\OmissionRequest;
use App\Http\Requests\Api\AlfaCRM\RecordRequest;
use App\Jobs\AlfaCRM\CameWithoutLead;
use App\Jobs\AlfaCRM\RecordWithLead;
use App\Jobs\AlfaCRM\RecordWithoutLead;
use App\Models\AlfaCRM\Setting;
use App\Models\AlfaCRM\Transaction;
use App\Models\Webhook;
use App\Services\AlfaCRM\Client;
use App\Services\AlfaCRM\Client as alfaApi;
use App\Services\AlfaCRM\Mapper;
use App\Services\AlfaCRM\Models\Lesson;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AlfaCRMController extends Controller
{
    private alfaApi $alfaApi;

    public function record(Webhook $webhook, RecordRequest $request, Transaction $transaction)
    {
        $data = $request->leads['status'][0] ?? $request->leads['add'][0];

        $transaction->setRecordData($data, $webhook);

        try {
            $setting = $webhook
                ->settings(Setting::class)
                ->firstOrFail();

            if($setting->checkStatus('record', $data['status_id'])) {

                if ($setting->work_lead == true) {

                    RecordWithLead::dispatch($setting, $webhook, $transaction, $data);
                } else
                    RecordWithoutLead::dispatch($setting, $webhook, $transaction, $data);
            }
        } catch (ModelNotFoundException $exception) {

            //TODO баг нет настроек
        } catch (\Throwable $exception) {

            $transaction->error = $exception->getMessage().' '.$exception->getFile().' '.$exception->getLine();
            $transaction->save();
        }
    }

    public function came(Webhook $webhook, CameRequest $request)
    {
        try {
            $this->alfaApi = (new alfaApi($webhook->user->alfaAccount()))->init();

            $setting = $webhook
                ->settings(Setting::class)
                ->firstOrFail();

            $this->alfaApi->branchId = $request->branch_id;

            $lesson = (new Lesson($this->alfaApi))
                ->get(
                    $request->entity_id,
                    Lesson::LESSON_CAME_TYPE_ID,
                );

            if ($lesson) {

                if ($lesson->status == Lesson::LESSON_CAME_TYPE_ID &&
                    $lesson->lesson_type_id == Lesson::LESSON_TYPE_ID) {

                    $transaction = $webhook->user
                        ->alfaTransactions()
                        ->where('alfa_branch_id', $request->branch_id)
                        ->where('alfa_client_id', $lesson->details[0]->customer_id)
                        ->where('status', Mapper::RECORD)
                        ->firstOrCreate([
                            'alfa_branch_id' => $request->branch_id,
                            'alfa_client_id' => $request->entity_id,
                            'user_id' => $webhook->user->id,
                        ]);

                    $transaction->setCameData($request->toArray(), $webhook);

                    CameWithoutLead::dispatch($setting, $webhook, $transaction, $request->toArray());
                }
            }
            //TODO баг нет настроек
        } catch (\Throwable $exception) {

//            if (!empty($transaction)) {
//                $transaction->error = $exception->getMessage().' '.$exception->getFile().' '.$exception->getLine();
//                $transaction->save();
//            } else
                dd($exception->getMessage(). ' '.$exception->getLine());
        }
    }

    public function omission(Webhook $webhook, OmissionRequest $request)
    {
        try {

            $this->alfaApi = (new alfaApi($webhook->user->alfaAccount()))->init();

            $setting = $webhook
                ->settings(Setting::class)
                ->firstOrFail();

            $this->alfaApi->branchId = $request->branch_id;

            $lesson = (new Lesson($this->alfaApi))
                ->get(
                    $request->entity_id,
                    Lesson::LESSON_OMISSION_TYPE_ID,
                );

            if ($lesson) {

                if ($lesson->status == Lesson::LESSON_TYPE_ID &&
                    $lesson->lesson_type_id == Lesson::LESSON_OMISSION_TYPE_ID) {

                    $transaction = $webhook->user
                        ->alfaTransactions()
                        ->where('alfa_branch_id', $request->branch_id)
                        ->where('alfa_client_id', $lesson->details[0]['customer_id'])
                        ->where('status', Mapper::RECORD)
                        ->firstOrCreate([
                            'alfa_branch_id' => $request->branch_id,
                            'alfa_client_id' => $request->entity_id,
                            'user_id' => $webhook->user->id,
                        ]);

                    $transaction->setOmissionData($request->toArray(), $webhook);

                    CameWithoutLead::dispatch($setting, $webhook, $transaction, $request->toArray());

                    //TODO баг нет настроек
                }
            }
        } catch (\Throwable $exception) {

//            if (!empty($transaction)) {
//                $transaction->error = $exception->getMessage().' '.$exception->getFile().' '.$exception->getLine();
//                $transaction->save();
//            } else
            dd($exception->getMessage(). ' '.$exception->getLine());
        }
    }
}