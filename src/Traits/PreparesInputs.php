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

namespace Drewlabs\Validation\Traits;

use Closure;
use LogicException;
use Drewlabs\Contracts\Validator\ViewModel;

/**
 * @method mixed __call(string $name, $arguments)
 * @method self|void prepareForValidation()
 */
trait PreparesInputs
{
    /**
     * Before validating the current object execute this function to transform request inputs.
     *
     * @return static|ViewModel
     */
    final public function before()
    {
        return $this->transform(function($self) {
            if (!method_exists($self, 'prepareForValidation')) {
                return $self;
            }
            // Executes the prepareForValidation implementation
            $self->prepareForValidation();
            return $self;
        });
    }

    /**
     * Send the current instance though a projection function which must return the same instance or
     * a modified copy of the current instance
     * 
     * @param Closure $callback 
     * @return static|ViewModel
     * 
     * @throws LogicException 
     */
    public function transform(\Closure $callback)
    {
        if (!is_a($object = $callback(clone $this), static::class)) {
            throw new \LogicException('Transformating function must return an instance of the current class, '.(\is_object($object) && (null !== $object) ? \get_class($object) : \gettype($object)));
        }
        return $object;
    }
}
