<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Validation\Tests\Stubs;

use Drewlabs\Contracts\Auth\Authenticatable;

class AuthenticatableStub implements Authenticatable
{
    private $id;

    private $username;

    private $password;

    private $user_details;

    public function authIdentifierName()
    {
        return 'id';
    }

    public function authIdentifier()
    {
        return $this->{$this->authIdentifierName()};
    }

    public function authPassword()
    {
        return $this->{$this->authPasswordName()};
    }

    public function authPasswordName()
    {
        return 'password';
    }

    public function rememberToken($token = null)
    {
        if ($token) {
            $this->remember_token = $token;
        }

        return $this->remember_token;
    }

    public function rememberTokenName()
    {
        return 'remember_token';
    }

    public function getAuthUserName()
    {
        return $this->username;
    }

    public function getUserDetails()
    {
        return $this->user_details;
    }

    public function getFillables()
    {
        return [
            'username',
            'password',
            'id',
        ];
    }

    public function getGuarded()
    {
        return [];
    }

    public function createToken($name, array $scopes = [])
    {
        return drewlabs_core_random_app_key(128);
    }
}
