<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';

    protected $fillable = [
        'id',
        'album_id',
        'user_id',
        'photo',
    ];

    public function albums()
    {
        return $this->belongsTo('App\Album', 'album_id');
    }



}
