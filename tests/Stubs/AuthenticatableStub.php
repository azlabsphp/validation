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
use Drewlabs\Core\Helpers\Rand;

class AuthenticatableStub implements Authenticatable
{
    private $id;

    private $password;

    private $username;

    private $user_details;

    private $token;

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
            $this->token = $token;
        }

        return $this->token;
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
        return Rand::key(128);
    }
}
