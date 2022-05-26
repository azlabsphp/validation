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

namespace Drewlabs\Validator\Exceptions;

final class ValidationException extends \Exception
{
    /**
     * List of validation errors.
     *
     * @var array|mixed
     */
    private $errors_;

    public function __construct($errors)
    {
        $this->errors_ = $errors;
        parent::__construct('Input validation fails... Check the error property of the class to get the list of errors');
    }

    /**
     * Returns the list of validation errors.
     *
     * @return array|mixed
     */
    public function getErrors()
    {
        return $this->errors_;
    }
}
