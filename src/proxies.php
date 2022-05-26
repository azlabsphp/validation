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

namespace Drewlabs\Validator\Proxy;

use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Validator\CoreValidatable;
use Drewlabs\Validator\InputsValidator;
use Drewlabs\Validator\Validator;

/**
 * Creates a {@link CoreValidatable} class instance.
 *
 * @return CoreValidatable|mixed
 */
function ViewModel($clazz, ?Authenticatable $user = null, array $attributes = [], array $files = [])
{
    $object = \is_string($clazz) ? new $clazz() : $clazz;
    if (!\is_object($object) || !($object instanceof CoreValidatable)) {
        throw new \InvalidArgumentException('1st argument to '.__FUNCTION__.' must be a valid core validatable object or class name');
    }
    /**
     * @var mixed
     */
    $object = $object;
    if (method_exists($object, 'call')) {
        return $object->call('setUserResolver', [
            static function () use ($user) {
                return $user;
            },
        ])
        ->call('merge', [
            $attributes ?? [],
        ])
        ->call('files', [
            $files ?? [],
        ]);
    }
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
 * @param mixed $factory
 *
 * @throws \InvalidArgumentException
 *
 * @return InputsValidator
 */
function Validator($factory)
{
    if ((null === $factory) || !(\is_object($factory))) {
        throw new \InvalidArgumentException('Validator must be a valid PHP object');
    }

    return new Validator($factory);
}
