<?php

namespace App\Http\Controllers;

use App\Services\Telegram\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{
    public function form(Request $request): \Illuminate\Http\RedirectResponse
    {
//        Log::info(__METHOD__, $request->toArray());

        $message = 'Заявка с сайта!'.
            ' имя : '.$request->name.
            ' телефон : '.$request->phone.
            ' текст : '.$request->message;

        (new Client())->send('Фидбек из кабинета '.Auth::user()->email.' | сообщение : '.$message);

        return back()->with('status', 'Успех');
    }
}