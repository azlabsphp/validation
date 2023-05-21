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

use Drewlabs\Contracts\Validator\CoreValidatable;
use Drewlabs\Core\Helpers\Arr;

trait ViewModel
{
    use ArrayAccessible;
    use HasFileAttributes;
    use PreparesInputs;

    /**
     * Inputs container or parameters bag.
     *
     * @var array
     */
    private $inputs = [];

    /**
     * @param mixed $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

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
     * Creates an instance of the current class.
     *
     * @param array $attributes
     *
     * @return self|CoreValidatable|Validatable
     */
    public static function new($attributes = [])
    {
        return (new static)->merge($attributes ?? []);
    }

    /**
     * @return object|string
     */
    public function getModel()
    {
        return $this->model_;
    }

    /**
     * Set the attributes to validate on the validatable class.
     *
     * @return self
     */
    public function set(array $values = [])
    {
        $this->inputs = $values ?? [];
        return $this;
    }

    /**
     * Copy the current object modifying the body attribute
     * 
     * @param array $values 
     * 
     * @return self 
     */
    public function withBody(array $values = [])
    {
        $self = clone $this;
        $self->set($values ?? []);
        return $self;
    }

    /**
     * Merge the object inputs with some new values provided.
     * 
     * **Note** By default, the merge method, return a modified
     * copy of the object. To modify object internal state instead,
     * pass `true` as second parameter to the merge call `merge([...], true)`
     * or call the `update([...])` to modify the object internal state
     * 
     * @param array $values 
     * @param bool $mutate 
     * @return self 
     */
    public function merge(array $values = [], bool $mutate = false)
    {
        $self = $mutate ? $this : clone $this;
        $self->set(array_merge($this->inputs ?? [], $values ?? []));
        return $self;
    }

    /**
     * Update object internal state with the provided values
     * 
     * @param array $values
     * 
     * @return self 
     */
    public function update(array $values = [])
    {
        return $this->merge($values, true);
    }

    /**
     * Get a key from the user provided attributes.
     *
     * @param string $key
     *
     * @return array|mixed|null
     */
    public function get(string $key = null)
    {
        if (null === $this->inputs) {
            return [];
        }
        // If a key is provided get the key from the array
        if ($key) {
            return Arr::get($this->inputs, $key, null);
        }
        // else return the array
        return $this->inputs;
    }

    /**
     * Checks if the view model has a given key.
     *
     * @return bool
     */
    public function has(string $key)
    {
        if (null === $this->inputs) {
            return false;
        }
        return null !== ($this->inputs[$key] ?? null);
    }

    /**
     * Get an entry from the inputs attributes.
     *
     * @return mixed|array
     */
    public function input($key = null)
    {
        return $this->get($key);
    }

    /**
     * Return the list of items in the object cache.
     *
     * @param array|mixed|null $keys
     *
     * @return array
     */
    public function all($keys = null)
    {
        $inputs = array_replace_recursive(
            $this->input() ?? [],
            $this->allFiles() ?? []
        );
        if (!$keys || empty($keys)) {
            return $inputs;
        }
        $results = [];
        foreach (\is_array($keys) ? $keys : \func_get_args() as $key) {
            Arr::set($results, $key, Arr::get($inputs, $key));
        }

        return $results;
    }

    // #region Miscelanous methods

    /**
     * Returns the list of request inputs execept files contents.
     *
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->input() ?? [];
    }

    /**
     * Returns all request input except user provided keys.
     *
     * @param array $keys
     *
     * @return array
     */
    public function except($keys = [])
    {
        return Arr::except($this->all(), $keys ?? []);
    }
    // #endregion Miscelanous methods

}
