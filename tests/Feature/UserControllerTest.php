<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    //login
    /** @test */
    public function login_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('123456'),
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => '123456',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ]);
    }

    /** @test */
    public function login_with_incorrect_credentials()
    {
        $loginData = [
            'email' => 'jonjones@gmail.com',
            'password' => '123456',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Unauthorized']);
    }

    //create
    /** @test */
    public function registering_a_user_auth_accept()
    {

        $data = [
            'name' => 'Jon Jones',
            'email' => 'JonJones@example.com',
            'password' => '123456',
        ];
        
        $response = $this->basicData($data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in',
                 ]);
    }

    /**
   * @dataProvider user_bad_mail
   * @dataProvider user_bad_name
   * @dataProvider user_bad_password
   */
    public function registering_a_user_auth_bad_params(array $data)
    {

    $response = $this->basicData($data);

    $response->assertStatus(422);
            
    }

    //delete
    /** @test */
    public function delete_user_success()
    {
        [$user, $token] = $this->createUserToken();
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/user', []);
    
        $response->assertStatus(200)
        ->assertJson(['message' => 'User deleted successfully']);
    
        $this->assertDeleted($user);
    }

    /** @test */
    public function delete_user_failure_user_not_found()
    {
        [$user, $token] = $this->createUserToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/user/' . ($user->id + 1), []);

        $response->assertStatus(404)
        ->assertNotFound();
    }

    /** @test */
    public function delete_user_failure_invalid_token()
    {
        $token = 'invalid-token';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->deleteJson('/api/user', []);

        $response->assertStatus(401)
        ->assertUnauthorized();
    }

    //support methods
    private function basicData(array $data)
    {
    return $this->postJson('/api/register', $data);

    }

    private function createUserToken() 
    {
        $userInfo = User::factory()->definition();
        $user = new User($userInfo);
        $user->save();
        $token = JWTAuth::fromUser($user);

        return [$user, $token];
        
    }

     //providers
     public static function user_bad_mail()
     {
        $data = [
            'name' => "Jon Jones",
            'email' => 'JonJones',
            'password' => '123456',
        ];
        return [
            'bad_mail' => $data
        ];
     }

     public static function user_bad_name()
     {
        $data = [
            'name' => "Jon Jones",
            'email' => 'JonJones@gmail.com',
            'password' => '123456',
        ];
        return [
            'bad_mail' => $data
        ];
     }

     public static function user_bad_password()
     {
        $data = [
            'name' => "Jon Jones",
            'email' => 'JonJones@gmail.com',
            'password' => 123456,
        ];
        return [
            'bad_mail' => $data
        ];
     }  
}
