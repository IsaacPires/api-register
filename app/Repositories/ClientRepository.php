<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class ClientRepository
{
    public function createWithAddress(array $data)
    { 
        DB::beginTransaction();

        try {

            $address = Address::create([
                'street' => $data['address']['street'],
                'number' => $data['address']['number'],
                'neighborhood' => $data['address']['neighborhood'],
                'complement' => $data['address']['complement'] ?? null,
                'zip_code' => $data['address']['zip_code'],
            ]);
            
            $client = Client::create([
                'name' => $data['client']['name'],
                'email' => $data['client']['email'],
                'cpf' => $data['client']['cpf'],
                'phone_one' => $data['client']['phone_one'],
                'phone_two' => $data['client']['phone_two'] ?? null,
                'address_id' => $address->id,
            ]);

            $client->save();

            DB::commit();

            return [$client, $address];

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function findById($id)
    {   
        return Client::with('address')->find($id);
    }

    public function updateWithAddress($id, array $data)
    {
        $client = Client::find($id);
        
        if (isset($data['client'])) {
          $client->update($data['client']);
        }

        if (isset($data['address'])) {
            $client->address->update($data['address']);
        }

        return $client;
    }
}
