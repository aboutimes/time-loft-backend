<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'author',
        'write_at',
        'is_reprint',
        'reprint_url',
        'background_url',
        'content',
        'read_number',
        'like',
        'dislike',
        'is_top'
    ];

    protected $dateFormat = 'U';

    public function user()
    {
        return $this->belongsTo('\App\User');
    }
    public function category()
    {
        return $this->belongsTo('\App\Category');
    }
    public function tag()
    {
        return $this->belongsTo('\App\Tag');
    }
    public function footprints()
    {
        return $this->hasmany('\App\Footprint');
    }
}
