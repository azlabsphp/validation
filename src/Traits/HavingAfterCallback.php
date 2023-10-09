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

use Closure;
use Drewlabs\Validation\Exceptions\ValidationException;
use Drewlabs\Contracts\Validator\ViewModel;


trait HavingAfterCallback
{
    /**
     * @var \Closure
     */
    private $__CALLBACK__;


    /**
     * {@inheritDoc}
     * 
     * @param callable $callback 
     * 
     * @return self|static|ViewModel
     */
    public function after($callback)
    {
        $this->__CALLBACK__ = $callback;

        return $this;
    }

    /**
     * Execute validation an invoke the callback set using `after` method
     * 
     * @param Closure $project
     * 
     * @return mixed
     * 
     * @throws ValidationException 
     */
    public function through(\Closure $project)
    {
        $self = \call_user_func($project);
        if (null !== ($callback = $self->__CALLBACK__)) {
            if ($self->fails()) {
                throw new ValidationException($self->errors());
            }
            return \call_user_func($callback, $self);
        }
        return $self;
    }
}
