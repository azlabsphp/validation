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

trait ProvidesRulesFactory
{
   // #region Validation methods
    /**
     * Creates a fluent rules by applying a prefix to rules keys
     *
     * @param string|null $prefix
     * @param array $attributes
     * @param array $excepts
     * @return mixed
     */
    public static function createRules(string $prefix = null, array $attributes = [], array $excepts = [])
    {
        return static::callCreateRules(static::new($attributes ?? [])->rules(), $prefix, $excepts);
    }

    /**
     * Creates a fluent update rules by applying a prefix to rules keys
     * 
     * @param string|null $prefix 
     * @param array $attributes 
     * @param array $excepts 
     * @return array 
     */
    public static function createUpdateRules(string $prefix = null, array $attributes = [], array $excepts = [])
    {
        return static::callCreateRules(static::new($attributes ?? [])->updateRules(), $prefix, $excepts);
    }

    /**
     * Internal method for creating rules using the provided prefix
     *
     * @param array $rules
     * @param string|null $prefix
     * @param array $excepts
     * @return array
     */
    private static function callCreateRules(array $rules, string $prefix = null, array $excepts = [])
    {
        $rules = iterator_to_array(self::getRulesExcepts($rules, $excepts));
        return null === $prefix ? $rules : iterator_to_array(static::prefixRules($rules, $prefix));
    }

    /**
     * Rewrite `required` adding a prefix key to each rules
     * 
     * @param mixed $key 
     * @param mixed $value 
     * @param string $prefix 
     * @return string 
     */
    private static function reconstructRequiredRules($key, $value, string $prefix)
    {
        $values = array_map(function ($item) use ($prefix) {
            return "$prefix.$item";
        }, array_filter(explode(',', self::afterString("$key:", $value)), function ($item) {
            return !empty($item);
        }));
        return "$key:" . implode(',', $values);
    }

    /**
     * Produces an iterable of prefixed rules
     * 
     * @param array $rules 
     * @param string|null $prefix 
     * @return \Traversable<string, mixed, mixed, void> 
     */
    private static function prefixRules(array $rules, string $prefix = null)
    {
        foreach ($rules as $key => $value) {
            $value = array_map(function ($current) use ($prefix) {
                if (!is_string($current)) {
                    return $current;
                }
                if (false !== strpos($current, 'required_without:')) {
                    return static::reconstructRequiredRules('required_without', $current, $prefix);
                }
                if (false !== strpos($current, 'required_without_all:')) {
                    return static::reconstructRequiredRules('required_without_all', $current, $prefix);
                }
                if (false !== strpos($current, 'required_with:')) {
                    return static::reconstructRequiredRules('required_with', $current, $prefix);
                }
                if (false !== strpos($current, 'required_with_all:')) {
                    return static::reconstructRequiredRules('required_with_all', $current, $prefix);
                }
                return $current;
            }, is_string($value) ? explode('|', $value) : $value);
            yield "$prefix.$key" => $value;
        }
    }

    /**
     * Returns the string after the first occurence of the provided character.
     * 
     * @param string $character 
     * @param string $haystack 
     * @return string 
     */
    private static function afterString(string $character, string $haystack)
    {
        if (!\is_bool(strpos($haystack, $character))) {
            return substr($haystack, strpos($haystack, $character) + mb_strlen($character));
        }
        return '';
    }

    /**
     * Resolve the list of rules with the `$excepts` rules maked as not required
     * 
     * @param array $rules 
     * @param array $excepts 
     * @return \Traversable<string|int, mixed, mixed, void> 
     */
    private static function getRulesExcepts(array $rules, array $excepts)
    {
        foreach ($rules as $key => $value) {
            if (false !== array_search($key, $excepts)) {
                $current =  array_filter(is_array($value) ? $value : [$value], function ($item) {
                    return false === strpos($item, 'required');
                });
                yield $key => ['sometimes', ...$current];
                continue;
            }
            yield $key => $value;
        }
    }
    // #endregion Validation methods 
}