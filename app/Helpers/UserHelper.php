<?php

namespace App\Helpers;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

if (!function_exists('createUserToken')) {
    function createUserToken()
    {
        $userInfo = User::factory()->definition();
        $user = new User($userInfo);
        $user->save();
        $token = JWTAuth::fromUser($user);

        return [$user, $token];
    }
}
