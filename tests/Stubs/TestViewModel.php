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

namespace Drewlabs\Validator\Tests\Stubs;

use Drewlabs\Contracts\Validator\CoreValidatable;
use Drewlabs\Validator\Traits\ViewModel;

class TestViewModel implements CoreValidatable
{
    use ViewModel;

    private $model_ = TestModel::class;

    public function __construct()
    {
    }

    public function rules()
    {
        return [];
    }

    public function messages()
    {
        return [];
    }

    public function dynamicMethod()
    {
        return $this->getPrimaryKey();
    }
}
