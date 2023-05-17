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

use Drewlabs\Contracts\Validator\ValidatorFactory;

class FakeValidatorFactory implements ValidatorFactory
{
    public function make(...$args)
    {
        return (new FakeCoreValidator())->validate(...$args);
    }
}
