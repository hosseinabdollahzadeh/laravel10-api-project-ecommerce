<?php

namespace App\Repositories;

use App\Models\User;

class UserRepo
{
    public static function store($values)
    {
        return User::create([
            'name' => $values['name'],
            'email' => $values['email'],
            'password' => $values['password'],
            'address' => $values['address'],
            'cellphone' => $values['cellphone'],
            'postal_code' => $values['postal_code'],
            'province_id' => $values['province_id'],
            'city_id' => $values['city_id'],
        ]);
    }

    public static function findByEmail($email)
    {
        return User::query()->where('email', $email)->firstOrFail();
    }
}
