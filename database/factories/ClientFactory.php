<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'cpf' => "12332145609",
            'phone_one' =>"51999999999",
            'phone_two' =>"51999999979",
            'address_id' => AddressFactory::new()->create()->id,
        ];
    }
}