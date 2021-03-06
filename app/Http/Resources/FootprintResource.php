<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FootprintResource extends Resource
{
    public static function collection($resource)
    {
        return tap(new FootprintResourceCollection($resource), function ($collection) {
            $collection->collects = __CLASS__;
        });
    }

    /**
     * @var array
     */
    protected $hideFields = [];

    /**
     * @var array
     */
    protected $showFields = [];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->filterFields([
            'id' => $this->id,
            'desc'=>$this->desc,
            'lng'=>$this->lng,
            'lat' => $this->lat,
            'created_at' => strtotime($this->created_at),
        ]);
    }
    /**
     * Set the keys that are supposed to be filtered out.
     *
     * @param array $fields
     * @return $this
     */
    public function hide(array $fields)
    {
        $this->hideFields = $fields;
        return $this;
    }
    /**
     * Set the keys that are supposed to be filtered out.
     *
     * @param array $fields
     * @return $this
     */
    public function show(array $fields)
    {
        $this->showFields = $fields;
        return $this;
    }

    /**
     * Remove the filtered keys.
     *
     * @param $array
     * @return array
     */
    protected function filterFields($array)
    {
        if (!empty($this->showFields))
        {
            return collect($array)->only($this->showFields)->toArray();
        }
        return collect($array)->forget($this->hideFields)->toArray();
    }
}
