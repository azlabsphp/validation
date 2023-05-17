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

namespace Drewlabs\Validation;

use Drewlabs\Contracts\Validator\CoreValidatable;
use Drewlabs\Contracts\Validator\ExceptionalValidator;
use Drewlabs\Contracts\Validator\Validator as ContractsValidator;
use Drewlabs\Contracts\Validator\ValidatorFactory;
use Drewlabs\Overloadable\Overloadable;
use Drewlabs\Validation\Exceptions\ValidationException;
use Drewlabs\Validation\Traits\HavingAfterCallback;
use InvalidArgumentException;

/**
 * @method self|mixed validate(array $values, array $rules, ?array $messages = [], ?\Closure $callback = null)
 * @method self|mixed validate(string $validatable, array $values, ?\Closure $callback = null)
 * @method self|mixed validate(\Drewlabs\Contracts\Validator\CoreValidatable $validatable, array $values, ?\Closure $callback = null)
 * @method self|mixed validate(\Drewlabs\Contracts\Validator\ViewModel $validatable, ?\Closure $callback = null)
 */
final class ValidatorAdapter implements ContractsValidator, ExceptionalValidator
{
    use HavingAfterCallback;
    use Overloadable;

    /**
     * Model validation errors generated after validation.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Validator Factory.
     *
     * @var ValidatorFactory|\Closure
     */
    private $validator;
    /**
     * Indicate to the validator to load rules define in the updateRules() method of the {ValidatableContract} class.
     *
     * @var bool
     */
    private $updating;

    /**
     * Creates an instance of InputsValidator class.
     *
     * @param ValidatorFactory|\Closure $validator
     */
    public function __construct($validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ValidationException
     */
    public function validate(...$args)
    {
        // Reset errors bag on each validations
        $this->setErrors([]);

        return $this->overload($args, [
            function (array $rules, array $values, ?array $messages = [], \Closure $callback = null) {
                return $this->after($callback)->through(function () use ($values, $rules, $messages) {
                    $errors = FluentValidator::new($this->validator)->validate($values, $rules, $messages);
                    if (!empty($errors)) {
                        $this->setErrors($errors);
                    }
                    return $this;
                });
            },
            function (string $validatable, array $values, $callback = null) {
                if (class_exists($validatable)) {
                    return $this->after($callback)->through(function () use ($values, $validatable) {
                        $errors = ViewValidator::new($this->validator, $this->updating)->validate(new $validatable, $values);
                        if (!empty($errors)) {
                            $this->setErrors($errors);
                        }
                        $this->updating = false;
                        return $this;
                    });
                }
                throw new InvalidArgumentException(sprintf("%s must exist and must be instance of %", $validatable, CoreValidatable::class));
            },
            function (CoreValidatable $object, array $values, $callback = null) {
                return $this->after($callback)->through(function () use ($values, $object) {
                    $errors = ViewValidator::new($this->validator, $this->updating)->validate($object, $values);
                    if (!empty($errors)) {
                        $this->setErrors($errors);
                    }
                    $this->updating = false;
                    return $this;
                });
            },
            function (CoreValidatable $view, $callback = null) {
                return $this->after($callback)->through(function () use ($view) {
                    if (!\is_array($values = $this->getValues($this->beforeValidation($view)))) {
                        throw new InvalidArgumentException('Return type of all() or toArray() method must be a PHP array');
                    }
                    $errors = ViewValidator::new($this->validator, $this->updating)->validate($view, $values);
                    if (!empty($errors)) {
                        $this->setErrors($errors);
                    }
                    $this->updating = false;
                    return $this;
                });
            },
        ]);
    }


    public function fails()
    {
        return empty($this->errors) ? false : true;
    }


    public function errors()
    {
        return $this->errors;
    }

    /**
     * {@inheritDoc}
     */
    public function updating()
    {
        $this->updating = true;

        return $this;
    }

    /**
     * @deprecated Use `updating()` instead
     * 
     * {@inheritDoc}
     */
    public function setUpdate(bool $update)
    {
        $this->updating = $update;

        return $this;
    }

    /**
     * Returns the validation factory use to validate inputs or view model.
     *
     * @return ValidatorFactory|\Closure
     */
    public function getFactory()
    {
        return $this->validator;
    }

    /**
     * @param array|mixed $view
     *
     * @return array
     */
    private function getValues($view)
    {
        return \is_array($view) ? $view : $view->all();
    }

    /**
     * Validation errors setter.
     *
     * @return void
     */
    private function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Executes a callback before the validation get executed.
     *
     * @param mixed $view
     *
     * @return mixed
     */
    private function beforeValidation($view)
    {
        if (\is_object($view) && method_exists($view, 'before')) {
            return $view->before();
        }
        return $view;
    }
}
