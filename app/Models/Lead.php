<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';
    protected $fillable = ["contact_id", "pipeline_id", "assigned_to", "status"];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function salesPerson() {
        return $this->belongsTo(SalesPerson::class, 'assigned_to');
    }
}
