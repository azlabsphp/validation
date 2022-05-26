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

use Drewlabs\Validator\Exceptions\ValidationException;

trait HavingAfterCallback
{
    private $callback_;

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function after($callback)
    {
        if (null === $callback) {
            return $this;
        }
        if (drewlabs_core_is_callable($callback)) {
            $this->callback_ = $callback;

            return $this;
        }
        throw new \InvalidArgumentException('Parameter to the after method must be a callable instance');
    }

    private function validateAndRunCallback(\Closure $validationCallBack)
    {
        /** @var \Drewlabs\Validator\InputsValidator */
        $self = \call_user_func($validationCallBack);
        if ($this->callback_) {
            if ($self instanceof self && ($self->fails())) {
                throw new ValidationException($self->errors());
            }

            return \call_user_func($this->callback_, $self);
        }

        return $self;
    }
}
