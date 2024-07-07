<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClientService
{

    public function verifyData(Request $request)
    {
        $request = $this->treatData($request);

        try {
            $request->validate([
                'client.name' => 'required|string|max:255',
                'client.email' => 'required|email|unique:client,email',
                'client.cpf' => 'required|string|max:14|unique:client,cpf',
                'client.phone_one' => 'required|string|max:20',
                
                'address.street' => 'required|string|max:255',
                'address.number' => 'required|string|max:20',
                'address.neighborhood' => 'required|string|max:100',
                'address.zip_code' => 'required|string|max:10',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return response()->json(['errors' => $errors], 422); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500); 
        }
    }

    private function removeSpecialChar(string $data) 
    {
        return preg_replace('/[^0-9]/', '', $data);
    }

    private function treatData(Request $request): Request
    {
        $data = $request->all();
      
        $data['client']['cpf']       = isset($data['client']['cpf']) ? $this->removeSpecialChar($data['client']['cpf']): null;
        $data['client']['phone_one'] = isset($data['client']['phone_two']) ? $this->removeSpecialChar($data['client']['phone_one']) : null;
        $data['client']['phone_two'] = isset($data['client']['phone_two']) ? $this->removeSpecialChar($data['client']['phone_two']) : null;
        $data['address']['number']   = isset($data['client']['phone_two']) ? $this->removeSpecialChar($data['address']['number']) : null;
        $data['address']['zip_code'] = isset($data['client']['phone_two']) ? $this->removeSpecialChar($data['address']['zip_code']) : null;
        
        $newRequest = $request->merge($data); 

        return $newRequest;
    }
}