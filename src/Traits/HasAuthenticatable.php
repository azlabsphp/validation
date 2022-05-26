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

namespace Drewlabs\Validator\Traits;

use Drewlabs\Contracts\Auth\Authenticatable;

trait HasAuthenticatable
{
    /**
     * @var \Closure
     */
    private $userResolver_;

    /**
     * @return self
     */
    public function setUser(?Authenticatable $user = null)
    {
        return $this->setUserResolver(
            static function () use ($user) {
                return $user;
            }
        );
    }

    /**
     * Provide a closure that when invoked return an instance of {@link Authenticatable}  class.
     *
     * ```
     * $model = $model->setUserResolver(function() use () {
     *  // Returns an authenticatable object
     * });
     * ```
     *
     * @return self
     */
    public function setUserResolver(\Closure $resolver)
    {
        $this->userResolver_ = $resolver;

        return $this;
    }

    /**
     * Returns the authenticatable binded to the current object.
     * 
     * @param string|null $guard 
     * @return Authenticatable|mixed
     */
    public function user($guard = null)
    {
        return $this->userResolver_ ? ($this->userResolver_)($guard) : null;
    }
}
