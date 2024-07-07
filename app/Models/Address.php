<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'address';
    
    use HasFactory;

    protected $fillable = [
        'street',
        'number',
        'neighborhood',
        'complement',
        'zip_code',
    ];


    public function client()
    {
        return $this->hasOne(Client::class);
    }
}
