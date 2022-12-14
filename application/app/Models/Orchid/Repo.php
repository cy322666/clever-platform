<?php

declare(strict_types=1);

namespace App\Models\Orchid;

use Orchid\Screen\Repository;
use Countable;
use Illuminate\Support\Arr;

/**
 * Class Repository.
 */
class Repo extends Repository
{
    /**
     * @var int
     */
    protected $position = 0;
    
    /**
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getContent(string $key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }
    
    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }
}
