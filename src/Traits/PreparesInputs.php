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

use Closure;
use LogicException;

trait PreparesInputs
{
    /**
     * Before validating the current object execute this function to transform request inputs
     * 
     * @return self 
     */
    public final function before()
    {
        if (method_exists($this, 'prepareForValidation')) {
            $self = method_exists($this, 'clone') ? $this->clone() : (clone $this);
            $self->prepareForValidation();
            return $self;
        }
        return $this;
    }

    /**
     * Applies a transformation function on the current instnce
     * 
     * @param Closure $callback 
     * @return mixed 
     */
    public function transform(\Closure $callback)
    {
        $classname = get_class($this);
        $object = $callback($this);
        if (!is_a($object, $classname)) {
            throw new LogicException('Transformating function must return an instance of the current class, ' . (is_object($object) && (null !== $object) ? get_class($object) : gettype($object)));
        }
        return $object;
    }
}
