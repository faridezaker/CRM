<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesPerson extends Model
{
    use HasFactory;

    protected $table = 'sales_person';

    protected $fillable = ["user_id","marketing_code"];

    public function leads()
    {
        return $this->hasMany(Lead::class,'sales_person_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
