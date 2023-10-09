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

use Drewlabs\Validation\Exceptions\ValidationException;
use Drewlabs\Contracts\Validator\Validator;
use Drewlabs\Contracts\Validator\ViewModel;
/**
 * @method mixed __call(string $name, $arguments)
 */
trait Validatable
{
    // #region Validation methods
    /**
     * Validates the view model object using the bounded validator {@see Validator} instance.
     *
     * if the view model provides updateRules(), passing $updating=true will load the update
     * rules
     *
     * ```php
     * <?php
     * $viewmodel = new ViewModelClass($request);
     *
     * // Validating
     * $viewmodel = $viewmodel->validated();
     *
     * // To execute a method after validating the model
     * $viewmodel->validated(function() use ($viewmodel) {
     *  // Persist data to database after validation
     * });
     *
     * // In order to use update rules
     * // This will throw an exception if the validation fails
     * $viewmodel->validated(true);
     *
     * // In order to use the update rules and pass a callback
     * // which runs when validation passes
     * $viewmodel->validated(true, function() use ($viewmodel) {
     *  // Persist data to database after validation
     * });
     * ```
     * @param bool|\Closure|null $updating
     * @param \Closure|null $callback
     * @throws \Drewlabs\Core\Validator\Exceptions\ValidationException
     * 
     * @return static|ViewModel
     */
    public function validated(...$args)
    {
        if (($count = count($args)) === 1 && !is_string($args[0]) && is_callable($args[0])) {
            return $this->validateThen($this->getValidator(), $args[0]);
        }
        if ($count === 1 && boolval($args[0]) === true) {
            return $this->validate($this->getValidator()->updating());
        }
        if ($count > 1) {
            return $this->validate($this->getValidator()->updating(), $args[1]);
        }
        return $this->validate($this->getValidator());
    }

    /**
     * Validates the current instance and throws exception if the validation fails.
     * 
     * **Note** Case a callback is provided, the callback is invoked on the current validator
     * instance
     * 
     * ```php
     * // Calling the validator without a callback
     * 
     * $view = new MyViewModel();
     * 
     * // This call throws an exception if validation fails or return an instance of the view model
     * $view = $view->validate(new ValidatorAdapter(...));
     * 
     * 
     * // Calling the validator with a callback
     * 
     * // This call throws an exception if validation fails or return the result of the callback
     * $result = $view->validate(new ValidatorAdapter(...), function($view) {
     *  // Interact with the view
     *  return new Response();
     * });
     * ```
     * 
     * @param Validator $validator
     * @param callable $callback
     * 
     * @return static|mixed
     * 
     * @throws ValidationException
     */
    public function validate(Validator $validator, callable $callback = null)
    {
        if (($validator = $validator->validate($this)) && $validator->fails()) {
            throw new ValidationException($validator->errors());
        }
        return null !== $callback ? $callback($this) : $this;
    }

    /**
     * Validates the current instance with a callback function
     * 
     * @param Validator $validator 
     * @param Closure $callback 
     * @return Validator 
     */
    private function validateThen(Validator $validator, \Closure $callback)
    {
        return $validator->validate($this, $callback);
    }
    //#endregion Validation methods
}