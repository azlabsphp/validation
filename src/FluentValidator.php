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

use Drewlabs\Contracts\Validator\ValidatorFactory;
use InvalidArgumentException;

final class FluentValidator
{

    /**
     * 
     * @var callable
     */
    private $factory;

    /**
     * Creates class instance
     * 
     * @param callable $factory 
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Creates new class instance
     * 
     * @param callable|ValidatorFactory $factory 
     * 
     * @return static 
     */
    public static function new($factory)
    {
        $factory = is_callable($factory) ? $factory : function (array $values, array $rules, array $messages = []) use ($factory) {
            if (!($factory instanceof ValidatorFactory)) {
                throw new InvalidArgumentException(sprintf("Expect parameter to be a %s instance, got %s", ValidatorFactory::class, is_object($factory) && !is_null($factory) ? get_class($factory) : gettype($factory)));
            }
            return $factory->make($values, $rules, $messages);
        };
        return new self($factory);
    }

    /**
     * Executes validation and returns the validation errors
     * 
     * @param array $values 
     * @param array $rules 
     * @param array $messages
     * 
     * @return array 
     */
    public function validate(array $values, array $rules, array $messages = [])
    {
        $validator = ($this->factory)($values, $rules, $messages);
        return $validator->fails() ? $validator->errors() ?? [] : [];
    }
}
