<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'client';

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'phone_one',
        'phone_two',
        'permission',
        'address_id'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
