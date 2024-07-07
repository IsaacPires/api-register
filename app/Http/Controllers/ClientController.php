<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private $clientRepository;

    private $clientService;


    public function __construct(ClientRepository $clientRepository, ClientService $clientService)
    {
        $this->clientRepository = $clientRepository;
        $this->clientService = $clientService;

    }

    public function store(Request $request)
    {
        $verify = $this->clientService->verifyData($request);

        if($verify){
            return $verify;
        }

        [$client, $address] = $this->clientRepository->createWithAddress($request->all());

        return response()->json(['message' => 'Customer and address created successfully', 'data' => [$client, $address]], 201);
    }

    public function show(int $id)
    {   
        $client = $this->clientRepository->findById($id);
        
        return response()->json(['data' => $client], 200);
    }

    public function update(Request $request, $id)
    {        
        $client = $this->clientRepository->updateWithAddress($id, $request->all());

        return response()->json(['message' => 'Customer and address updated successfully', 'data' => $client], 200);
    }

    public function destroy(Client $client)
    {
        try {
            $client->delete();
            return response()->json(['message' => 'Client deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete client'], 500);
        }
    }

}
