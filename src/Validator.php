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

namespace Drewlabs\Validator;

use Drewlabs\Contracts\Validator\CoreValidatable;
use Drewlabs\Contracts\Validator\ExceptionalValidator;
use Drewlabs\Contracts\Validator\Validator as ContractsValidator;
use Drewlabs\Contracts\Validator\ValidatorFactory;
use Drewlabs\Overloadable\Overloadable;
use Drewlabs\Validator\Exceptions\ValidationException;
use Drewlabs\Validator\Traits\HavingAfterCallback;
use Drewlabs\Validator\Traits\ValidatesArray;
use Drewlabs\Validator\Traits\ValidatesViewModel;

/**
 * @method self|mixed validate(array $values, array $rules, ?array $messages = [], ?\Closure $callback = null)
 * @method self|mixed validate(string $validatable, array $values, ?\Closure $callback = null)
 * @method self|mixed validate(\Drewlabs\Contracts\Validator\CoreValidatable $validatable, array $values, ?\Closure $callback = null)
 * @method self|mixed validate(\Drewlabs\Contracts\Validator\CoreValidatable $validatable, ?\Closure $callback = null)
 */
class Validator implements ContractsValidator, ExceptionalValidator
{
    use HavingAfterCallback;
    use Overloadable;
    use ValidatesArray;
    use ValidatesViewModel;

    /**
     * Model validation errors generated after validation.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Validator Factory.
     *
     * @var \Illuminate\Contracts\Validation\Factory|ValidatorFactory
     */
    private $validator;

    /**
     * Creates an instance of InputsValidator class.
     *
     * @param \Illuminate\Contracts\Validation\Factory|ValidatorFactory|ValidatorFactory $validator
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
            function (array $rules, array $values, ?array $messages = [], ?\Closure $callback = null) {
                return $this->after($callback)->validateAndRunCallback(function () use ($values, $rules, $messages) {
                    return $this->validateArray($values, $rules, $messages);
                });
            },
            function (string $validatable, array $values, $callback = null) {
                if (class_exists($validatable)) {
                    $validatable = \function_exists('app') ? \call_user_func('app', $validatable) : new $validatable();

                    return $this->after($callback)->validateAndRunCallback(
                        function () use ($values, $validatable) {
                            return $this->validateModel($values, $validatable);
                        }
                    );
                }
                throw new \Exception('Class must be an instance of '.CoreValidatable::class);
            },
            function (CoreValidatable $validatable, array $values, $callback = null) {
                return $this->after($callback)->validateAndRunCallback(function () use ($values, $validatable) {
                    return $this->validateModel($values, $validatable);
                });
            },
            function (CoreValidatable $viewModel, $callback = null) {
                return $this->after($callback)->validateAndRunCallback(function () use ($viewModel) {
                    if (!\is_array($values = $this->getValues($viewModel))) {
                        throw new \Exception('Return type of all() or toArray() method must be a PHP array');
                    }

                    return $this->validateModel($values, $viewModel);
                });
            },
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function fails()
    {
        if (empty($this->errors)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Returns the validation factory use to validate inputs or view model.
     *
     * @return Illuminate\Contracts\Validation\Factory|ValidatorFactory
     */
    public function getFactory()
    {
        return $this->validator;
    }

    protected function getValues($viewModel)
    {
        if (!(method_exists($viewModel, 'all') || method_exists($viewModel, 'toArray'))) {
            throw new \Exception('Validatable class must define a all() or toArray() method that returns the array of values to validate');
        }

        return method_exists($viewModel, 'all') ?
            \call_user_func([$viewModel, 'all']) : (method_exists($viewModel, 'toArray') ?
                \call_user_func([$viewModel, 'toArray']) :
                []);
    }

    /**
     * Validation errors setter.
     *
     * @return void
     */
    protected function setErrors(array $errors)
    {
        $this->errors = $errors;
    }
}
