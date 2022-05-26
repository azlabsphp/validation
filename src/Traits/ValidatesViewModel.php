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

use Drewlabs\Contracts\Validator\CoreValidatable;

// @internal Used internally by {@see InputsValidator} class
trait ValidatesViewModel
{
    /**
     * Indicate to the validator to load rules define in the updateRules() method of the {ValidatableContract} class.
     *
     * @var bool
     */
    private $updating;

    /**
     * {@inheritDoc}
     */
    public function updating()
    {
        $this->updating = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdate(bool $update)
    {
        $this->updating = $update;

        return $this;
    }

    /**
     * Apply validation rules on array input using rules defines in a validatable model instance.
     *
     * @param CoreValidatable $validatable
     *
     * @return static
     */
    public function validateModel(array $values, $validatable)
    {
        $validator_inputs = $values;
        // Load the validation rules from the view model
        $validator = $this->validator->make(
            $validator_inputs,
            $this->getRules($validatable),
            $validatable->messages() ?? []
        );
        // Validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (null !== $errors) {
                $this->setErrors(\is_array($errors) ? $errors : $errors->messages());
            }
        }
        // Reset the update property when validation completes in order to not apply the same property value to the next call on the validtor
        $this->updating = false;
        // Return the object for methods chaining
        return $this;
    }

    /**
     * @param CoreValidatable|mixed $validatable
     */
    private function getRules($validatable): array
    {
        if ($this->updating && method_exists($validatable, 'updateRules')) {
            return $validatable->updateRules() ?? [];
        }

        return $validatable->rules() ?? [];
    }
}
