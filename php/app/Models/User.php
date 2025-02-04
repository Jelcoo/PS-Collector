<?php

namespace App\Models;

class User extends Model
{
    public int $id;
    public string $username;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $password;
    public string $created_at;

    public array $hidden = ['password'];

    public function __construct(array $user)
    {
        $this->id = $user['id'];
        $this->username = $user['username'];
        $this->first_name = $user['first_name'];
        $this->last_name = $user['last_name'];
        $this->email = $user['email'];
        $this->password = $user['password'];
        $this->created_at = $user['created_at'];
    }
}
