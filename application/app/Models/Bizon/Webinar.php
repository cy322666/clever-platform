<?php

namespace App\Models\Bizon;

use App\Models\Account;
use App\Models\Bizon\Setting;
use App\Services\amoCRM\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;

/**
 * @method static create(array $toArray)
 */
class Webinar extends Model
{
    use HasFactory, Filterable;

    protected $table = 'bizon_webinars';

    protected $fillable = [
        'event',
        'roomid',
        'webinarId',
        'stat',  //число зрителей
        'len',   //длительность вебинара
        'setting_id'
    ];

    //возможность сортировки по полям
    protected array $allowedSorts = [
        'status',
        'created_at',
        'updated_at',
    ];

    //возможность фильтрация по полям
    protected array $allowedFilters = [
        'status',
        'roomid',
        'created_at',
        'room_title',
    ];

    public function viewers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Viewer::class);
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function setViewers(array $report, Setting $setting, array $commentariesTS)
    {
        foreach ($report['usersMeta'] as $user_key => $user_array) {

            $viewTill = $user_array['viewTill'] ?? null;
            $view     = $user_array['view'] ?? null;

            $time = Viewer::getTime($viewTill, $view);

            $this->viewers()->create([

                'chatUserId' => $user_array['chatUserId'],
                'phone'      => $user_array['phone'],
                'webinarId'  => $user_array['webinarId'],
                'view'       => $view ?? null,
                'viewTill'   => $viewTill ?? null,
                'time'       => $time,
                'email'      => $user_array['email'],
                'username'   => $user_array['username'],
                'roomid'     => $user_array['roomid'],
                'type'       => Viewer::getType($setting, $time),
                'url'        => !empty($user_array['url']) ? mb_strimwidth($user_array['url'], 0, 100, "...") : null,
                'ip'         => $user_array['ip'],
                'useragent'  => $user_array['useragent'] ?? null,
                'created'    => $user_array['created'],
                'playVideo'  => $user_array['playVideo'] == 1 ? 'Да' : 'Нет',
                'finished'   => !empty($user_array['finished']) ? 'Да' : 'Нет',
                'messages_num' => $user_array['messages_num'],
                'cv'         => $user_array['cv'] ?? null,
                'cu1'        => $user_array['cu1'] ?? null,
                'p1'         => $user_array['p1'] ?? null,
                'p2'         => $user_array['p2'] ?? null,
                'p3'         => $user_array['p3'] ?? null,
                'referer'    => $user_array['referer'] ?? null,
                'city'       => $user_array['city'] ?? null,
                'region'     => $user_array['region'] ?? null,
                'country'    => $user_array['country'] ?? null,
                'tz'         => $user_array['tz'] ?? null,
                'utm_source' => $user_array['utm_source'] ?? null,
                'utm_medium' => $user_array['utm_medium'] ?? null,
                'utm_campaign' => $user_array['utm_campaign'] ?? null,

                'clickFile'   => $user_array['clickFile'] ?? null,
                'clickBanner' => !empty($user_array['clickBanner']) ? 'Да' : 'Нет',
                'commentaries'=> count($commentariesTS[$user_key]) > 0 ? json_encode($commentariesTS[$user_key]) : null,
            ]);
        }
    }
}
