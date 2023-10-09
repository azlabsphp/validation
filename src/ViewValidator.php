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
use Drewlabs\Contracts\Validator\ViewModel;
use Drewlabs\Contracts\Validator\Validatable;

final class ViewValidator
{
    /**
     * @var callable
     */
    private $factory;

    /**
     * @var bool
     */
    private $updating = false;

    /**
     * Creates class instance
     * 
     * @param callable $factory 
     * @param bool $updating
     */
    public function __construct(callable $factory, bool $updating)
    {
        $this->factory = $factory;
        $this->updating = $updating;
    }


    /**
     * Creates new class instance
     * 
     * @param callable|ValidatorFactory $factory
     * 
     * @param bool $updating
     * 
     * @return static 
     */
    public static function new($factory, bool $updating = null)
    {
        $factory = is_callable($factory) ? $factory : function (array $values, array $rules, array $messages = []) use ($factory) {
            if (!($factory instanceof ValidatorFactory)) {
                throw new InvalidArgumentException(sprintf("Expect parameter to be a %s instance, got %s", ValidatorFactory::class, is_object($factory) && !is_null($factory) ? get_class($factory) : gettype($factory)));
            }
            return $factory->make($values, $rules, $messages);
        };
        return new self($factory, $updating ?? false);
    }

    /**
     * Validates provided values using the view model instance
     * 
     * @param ViewModel $view
     * @param array $values 
     * 
     * @return array 
     */
    public function validate($view, array $values)
    {
        $validator = ($this->factory)($values, $this->getRules($view), \is_array($view->messages()) ? $view->messages() : []);
        return $validator->fails() ? $validator->errors() ?? [] : [];
    }

    /**
     * @param ViewModel|Validatable $view
     * 
     * @return array
     */
    private function getRules($view): array
    {
        return $this->updating && method_exists($view, 'updateRules') ? $view->updateRules() ?? [] : $view->rules() ?? [];
    }
}
