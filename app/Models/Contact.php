<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = ["name","email","mobile","address"];

    public function leads() {
        return $this->hasMany(Lead::class,'contact_id');
    }

}
