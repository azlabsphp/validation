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

use Drewlabs\Contracts\Validator\CoreValidatable;
use Drewlabs\Validation\Traits\ViewModel;

class BeforeValidationViewModel implements CoreValidatable
{
    use ViewModel;

    private $model_ = TestModel::class;

    public function rules()
    {
        return [
            'details' => 'required|string',
        ];
    }

    public function messages()
    {
        return [];
    }

    protected function prepareForValidation()
    {
        $this->update([
            'details' => json_encode($this->details),
        ]);
    }
}
