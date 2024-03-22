<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextBlock extends Model
{
    protected $table = 'text_blocks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category', 'text'
    ];
}
