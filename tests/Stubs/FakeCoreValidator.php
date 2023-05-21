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

namespace Drewlabs\Validation\Tests\Stubs;

use Drewlabs\Contracts\Validator\CoreValidator;

class FakeCoreValidator implements CoreValidator
{
    /**
     * @var array
     */
    private $errors_;

    public function validate(...$args)
    {
        $values = $args[0] ?? [];
        $rules = $args[1] ?? [];
        // $message = $args[2] ?? [];
        foreach ($rules as $key => $value) {
            $value = \is_string($value) ? explode('|', $value) : $value;
            if (\in_array('required', $value, true) && (null === ($values[$key] ?? null))) {
                $this->errors_["$key.required"] = "$key attribute is required";
            }

            if (\in_array('numeric', $value, true) && !is_numeric(($values[$key] ?? null))) {
                $this->errors_["$key.numeric"] = "$key attribute must be a numeric value";
            }
            if (\in_array('string', $value, true) && !\is_string($values[$key])) {
                $this->errors_["$key.string"] = "$key attribute must be a string value";
            }
        }

        return $this;
    }

    public function fails()
    {
        return true;
    }

    public function errors()
    {
        return $this->errors_;
    }
}
