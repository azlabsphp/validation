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

use Drewlabs\Core\Helpers\Arr;

trait ViewModel
{
    use AccessibleViewModel;
    use HasAuthenticatable;
    use HasFileAttributes;

    /**
     * Inputs container or parameters bag.
     *
     * @var array
     */
    private $inputs = [];

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __call($name, $arguments)
    {
        $model = $this->model_ ?? $this->getModel();
        // We create an instance of the model class if it's a string
        if (\is_string($model) && class_exists($model)) {
            $model = new $model();
        }
        if ($model) {
            return $model->{$name}(...$arguments);
        }
        throw new \BadMethodCallException("Method $name does not exists on ".__CLASS__);
    }

    /**
     * @param mixed $model
     *
     * @return self
     */
    public function setModel($model)
    {
        $this->model_ = $model;

        return $this;
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
        return $this->withBody($values ?? []);
    }

    /**
     * Set the attributes to validate on the validatable class.
     *
     * @return self
     */
    public function withBody(array $values = [])
    {
        $this->inputs = $values ?? [];

        return $this;
    }

    /**
     * Merge the object inputs with some new values provided.
     *
     * @return self
     */
    public function merge(array $values = [])
    {
        $this->inputs = array_merge($this->inputs ?? [], $values ?? []);

        return $this;
    }

    /**
     * Get a key from the user provided attributes.
     *
     * @param string $key
     *
     * @return array|mixed|null
     */
    public function get(?string $key = null)
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

    /**
     * Add / Set value of the provided key to equals the id if the currently
     * connected user.
     *
     * @return self
     */
    public function setAuthUserInput(string $key)
    {
        return $this->merge([
            $key => (null !== ($user = $this->user())) ? $user->authIdentifier() : null,
        ]);
    }
    // #endregion Miscelanous methods
}
