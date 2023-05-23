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

trait ModelAware
{

    /**
     * Override function call if function does not exists on the current class.
     *
     * @param mixed $name
     * @param mixed $arguments
     *
     * @throws BadMethodCallException
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // We create an instance of the model class if it's a string
        if (\is_string($model = $this->getModel()) && class_exists($model)) {
            $model = new $model;
        }
        if ($model) {
            return $model->{$name}(...$arguments);
        }
        throw new \BadMethodCallException("Method $name does not exists on " . __CLASS__);
    }

    /**
     * @return object|string
     */
    public function getModel()
    {
        return $this->model_;
    }
}
