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

// @internal Used internally by {@see InputsValidator} class
trait ValidatesArray
{
    /**
     * Apply validation rules to an array input based on rules defines in an associative array.
     *
     * @return void
     */
    public function validateArray(array $values, array $rules, array $messages = [])
    {
        $validator = $this->validator->make($values, $rules, $messages);
        // Validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (null !== $errors) {
                $this->setErrors(\is_array($errors) ? $errors : $errors->messages());
            }
        }

        return $this;
    }
}
