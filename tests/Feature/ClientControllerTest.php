<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Client;
use App\Models\Address;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Mockery;


use function App\Helpers\createUserToken;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $clientRepository;
    protected $clientService;

    private $noClient = 1000;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientService = Mockery::mock('App\Services\ClientService');
        $this->clientRepository = Mockery::mock('App\Repositories\ClientRepository');
        
        $this->app->instance('App\Services\ClientService', $this->clientService);
        $this->app->instance('App\Repositories\ClientRepository', $this->clientRepository);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function returns_client_data_success()
    {
        [$client] = $this->createClient();
        [$user, $token] = createUserToken();
        $client->load('address');

        $this->clientRepository->shouldReceive('findById')->with($client->id)->andReturn($client);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json',
        ])->getJson(route('client.show', $client->id));
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'cpf',
                    'phone_one',
                    'phone_two',
                    'address' => [
                        'street',
                        'number',
                        'neighborhood',
                        'complement',
                        'zip_code',
                    ]
                ]
            ]);
    }

    /** @test */
    public function returns_client_not_found()
    {
        [$user, $token] = createUserToken();


        $this->clientRepository
            ->shouldReceive('findById')
            ->with($this->noClient)
            ->andReturn(null);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json',
        ])->getJson(route('client.show', $this->noClient));
        
        $response->assertStatus(404);
    }

    /** @test */
    public function destroy_client_success()
    {
        $client = Client::factory()->create();

        [$user, $token] = createUserToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson(route('client.destroy', $client->id));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Client deleted successfully',
            ]);
    }

    /** @test */
    public function destroy_client_not_found()
    {
        [$user, $token] = createUserToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson(route('client.destroy', $this->noClient));

        $response->assertStatus(404);
    }

    /** @test */
    public function update_client_success()
    {
        $client = Client::factory()->create();

        $updatedData = [
            'name' => 'Conor mcgregor',
            'email' => 'conor@gmail.com',
        ];

        [$user, $token] = createUserToken();

        $this->clientRepository
             ->shouldReceive('updateWithAddress')
             ->with($client->id, $updatedData)
             ->andReturn($client);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson(route('client.update', $client->id), $updatedData);

        $response->assertStatus(200);
    }

    /** @test */
    public function update_client_failure_client_not_found()
    {
        $updatedData = [
            'name' => 'Novo Nome do Cliente',
            'email' => 'novoemail@example.com',
        ];

        [$user, $token] = createUserToken();

        $this->clientRepository->shouldReceive('updateWithAddress')->with($this->noClient, $updatedData)->andReturn(null);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson(route('client.update', $this->noClient), $updatedData);

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Client not found',
            ]);
    }

    /** @test */
    public function store_client_success()
    {
        $requestData = [
            "client"=>[
                'name' => 'Novo Cliente',
                'email' => 'novocliente@example.com',
                'cpf' => '123.456.789-00',
                'phone_one' => '(99) 9999-9999',
                'phone_two' => '(88) 8888-8888',
            ],
            'address' => [
                'street' => 'Rua Nova',
                'number' => '123',
                'neighborhood' => 'Bairro Novo',
                'complement' => 'Apto 101',
                'zip_code' => '12345-678',
            ],
        ];

        $client = Client::factory()->make($requestData['client']);
        $address = Address::factory()->make($requestData['address']);

        [$user, $token] = createUserToken();

        $this->clientService->shouldReceive('verifyData')->with(Mockery::on(function($request) use ($requestData) {
            return true;
        }))->andReturn(null);

        $this->clientRepository->shouldReceive('createWithAddress')->with(Mockery::on(function($data) use ($requestData) {
            return $data == $requestData;
        }))->andReturn([$client, $address]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson(route('client.store'), $requestData);

        $response->assertStatus(201);
    }

    /** @test */
    public function store_client_failure_validation()
    {
        $requestData = [
            "client"=>[
                'name' => '',
                'email' => 'novocliente@example.com',
                'cpf' => '123.456.789-00',
                'phone_one' => '(99) 9999-9999',
                'phone_two' => '(88) 8888-8888',
            ],
            'address' => [
                'treet' => 'Rua street',
                'number' => '123',
                'neighborhood' => 'Bairro neighborhood',
                'complement' => 'casa',
                'zip_code' => '12345-678',
            ],
        ];

        [$user, $token] = createUserToken();

        $this->clientService->shouldReceive('verifyData')->with(Mockery::any())->andReturn(response()->json(['errors' => [
            'client.name' => ['The client name field is required.'],
        ]], 422));

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json',
        ])->postJson(route('client.store'), $requestData);

        $response->assertStatus(422);
    }

    //basic
    private function createClient()
    {
        $address = Address::factory()->create([
            'street' => 'Rua Exemplo',
            'number' => 123,
            'neighborhood' => 'Centro',
            'complement' => 'Sala 101',
            'zip_code' => "91110010",
        ]);

        $client = Client::factory()->create([
            'name' => 'João Silva2',
            'email' => 'joao@example2.com',
            'cpf' => '12332112393',
            'phone_one' => '11987654321',
            'phone_two' => '11912345678',
            'address_id' => $address->id,
        ]);

        return [$client, $address];
    }
}