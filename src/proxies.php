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

namespace Drewlabs\Validation\Proxy;

use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Validator\CoreValidatable;
use Drewlabs\Validation\ValidatorAdapter;
use Drewlabs\Contracts\Validator\ValidatorFactory;
use Closure;
use InvalidArgumentException;


/**
 * Creates a {@link CoreValidatable} class instance.
 * 
 * @param mixed $blueprint 
 * @param Authenticatable|null $user 
 * @param array $attributes 
 * @param array $files 
 * @return mixed 
 * @throws InvalidArgumentException 
 */
function ViewModel($blueprint, Authenticatable $user = null, array $attributes = [], array $files = [])
{
    $object = \is_string($blueprint) ? new $blueprint() : $blueprint;
    if (!\is_object($object) || !($object instanceof CoreValidatable)) {
        throw new \InvalidArgumentException('1st argument to '.__FUNCTION__.' must be a valid core validatable object or class name');
    }
    /**
     * @var mixed $object
     */
    if (method_exists($object, 'setUserResolver') && (null !== $user)) {
        $object = $object->setUserResolver(static function () use ($user) {
            return $user;
        });
    }
    // Merge in the attributes
    if (method_exists($object, 'merge') && (null !== $attributes)) {
        $object = $object->merge($attributes ?? []);
    }
    if (method_exists($object, 'files') && (null !== $files)) {
        $object = $object->files($files ?? []);
    }

    return $object;
}

/**
 * Creates an instance of the Validator class.
 *
 * @param ValidatorFactory|\Closure $factory
 *
 * @throws \InvalidArgumentException
 *
 * @return ValidatorAdapter
 */
function Validator($factory)
{
    if ((null === $factory) || !(\is_object($factory))) {
        throw new \InvalidArgumentException('Validator must be a valid PHP object');
    }
    return new ValidatorAdapter($factory);
}
