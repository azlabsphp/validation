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

use Drewlabs\Contracts\Validator\ViewModel as ValidatorViewModel;
use Drewlabs\Validation\Traits\ProvidesRulesFactory;
use Drewlabs\Validation\Traits\ValidatableViewModel;

class TestViewModel implements ValidatorViewModel
{
    use ValidatableViewModel;
    use ProvidesRulesFactory;

    private $model_ = TestModel::class;

    public function rules()
    {
        return [
            'prop_1' => ['nullable', 'numeric'],
            'prop_2' => ['nullable', 'string']
        ];
    }

    public function messages()
    {
        return [];
    }
}
