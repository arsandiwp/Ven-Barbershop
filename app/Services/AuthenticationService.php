<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticationService
{
    public function authenticate($email, $password)
    {
        $userData = User::where('email', $email)->first();
        if (!$userData) {
            return false;
        }

        if (!Hash::check($password, $userData->password)) {
            return false;
        }

        return (object) $userData;
    }

    public function generateToken()
    {
        return md5(rand(1, 10) . microtime());
    }

    public function setTokenData($key, $value)
    {
        Redis::set($key, json_encode($value));
    }

    public function getTokenData($key)
    {
        return (array) (json_decode(Redis::get($key)));
    }

    public function removeToken($key)
    {
        return Redis::del($key);
    }

    public function removeAllToken()
    {
        return Redis::flushDB();
    }
}
