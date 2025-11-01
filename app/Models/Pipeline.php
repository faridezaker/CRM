<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    protected $table = 'pipelines';

    protected $fillable = ["name","slug"];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
