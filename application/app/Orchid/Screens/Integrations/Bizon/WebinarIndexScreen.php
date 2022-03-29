<?php

namespace App\Orchid\Screens\Integrations\Bizon;

use App\Models\Api\Integrations\Bizon\Webinar;
use App\Orchid\Layouts\WebinarIndexLayout;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Action;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;

class WebinarIndexScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Список вебинаров';

    /**
     * Display header description.
     *
     * @var string|null
     */
    public $description = 'Список вебинаров Бизон 365';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'webinars' => Webinar::where('account_id', Auth::user()->account->id)
                ->orderBy('created_at', 'desc')
                ->paginate(),
        ];
    }
    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [
//            Button::make('Экспорт')
//                ->method('export')
//                ->icon('cloud-download')
//                ->rawClick()
//                ->novalidate(),
        ];
    }
    
    /**
     * Views.
     *
     * @return array
     */
    public function layout(): array
    {
        return [
            WebinarIndexLayout::class,
        ];
    }
    
    public function export()
    {
//        return response()->streamDownload(function () {
//            $csv = tap(fopen('php://output', 'wb'), function ($csv) {
//                fputcsv($csv, ['header:col1', 'header:col2', 'header:col3']);
//            });
//
//            collect([
//                ['row1:col1', 'row1:col2', 'row1:col3'],
//                ['row2:col1', 'row2:col2', 'row2:col3'],
//                ['row3:col1', 'row3:col2', 'row3:col3'],
//            ])->each(function (array $row) use ($csv) {
//                fputcsv($csv, $row);
//            });
//
//            return tap($csv, function ($csv) {
//                fclose($csv);
//            });
//        }, 'webinars '.date('Y-m-d H:i:s').'.csv');
    }
}