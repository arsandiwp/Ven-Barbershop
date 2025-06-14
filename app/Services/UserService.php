<?php

namespace App\Services;

use App\Models\User as UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class UserService
{
    const PRIMARY_KEY = 'id';
    const DEFAULT_PER_PAGE = 25;
    private $userModel;
    private $fileHandlerService;

    public function __construct(UserModel $userModel, FileHandlerService $fileHandlerService)
    {
        $this->userModel = $userModel;
        $this->fileHandlerService = $fileHandlerService;
    }

    public function getWith()
    {
        return $this->userModel::With('role');
    }

    public function makePassword($password = "123456")
    {
        $hashPassword = Hash::make($password);
        return $hashPassword;
    }

    public function getByEmail($email)
    {
        return $this->userModel::where('email', $email)->whereNull('deleted_at');
    }

    public function getPaginate($per_page, $keyword)
    {
        $container = $this->getWith();
        if ($keyword) {
            $container = $container->where(function ($q) use ($keyword) {
                $q->where('name', "like", "%" . $keyword . "%");
                $q->orWhere('email', "like", "%" . $keyword . "%");
            });
        }
        return $container->paginate($per_page ?? self::DEFAULT_PER_PAGE);
    }

    public function get($user_id)
    {
        return $this->userModel::where('id', $user_id)->get();
    }

    public function create($data)
    {
        return $this->userModel::create($data)->id;
    }

    public function update($id, $data)
    {
        return $this->userModel::where(self::PRIMARY_KEY, $id)->update($data);
    }

    public function delete($id)
    {
        return $this->userModel::where(self::PRIMARY_KEY, $id)->delete();
    }

    public function restore($id)
    {
        return $this->userModel::withTrashed()->find($id)->restore();
    }

    public function getRandomString($ln = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $ln; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    public function getByToken(string $token)
    {
        $keys = Cache::get('password_reset_keys', []);

        foreach ($keys as $key) {
            $cachedToken = Cache::get($key);

            if ($cachedToken === $token) {
                $userId = str_replace('password_reset_token_', '', $key);

                $user = $this->userModel->find($userId);
                if ($user) {
                    return $user;
                }
            }
        }

        // throw new CustomException("Token reset password tidak valid atau sudah kadaluarsa.");
    }

    public function storePasswordResetToken($userId, $token)
    {
        $key = 'password_reset_token_' . $userId;

        Cache::put($key, $token, now()->addMinutes(60));

        $keys = Cache::get('password_reset_keys', []);
        if (!in_array($key, $keys)) {
            $keys[] = $key;
            Cache::put('password_reset_keys', $keys, now()->addDay());
        }

        return $token;
    }

    public function removePasswordResetToken($userId)
    {
        $key = 'password_reset_token_' . $userId;

        Cache::forget($key);

        $keys = Cache::get('password_reset_keys', []);
        $keys = array_diff($keys, [$key]);
        Cache::put('password_reset_keys', $keys, now()->addDay());
    }

    public function deleteMemberItemStorage($id)
    {
        $email = $this->get($id)->first()->email;
        $status = $this->delete($id);
        if ($status) {
            return $this->fileHandlerService->deleteFileItemDirectory("public/user/" . $email);
        } else {
            return 0;
        }
    }
}
