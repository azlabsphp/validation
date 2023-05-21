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

namespace Drewlabs\Validation\Tests\Unit;

use Drewlabs\Validation\Exceptions\ValidationException;
use function Drewlabs\Validation\Proxy\Validator;

use Drewlabs\Validation\Tests\Stubs\BeforeValidationViewModel;
use Drewlabs\Validation\Tests\Stubs\CoreValidatableModel;
use Drewlabs\Validation\Tests\Stubs\FakeValidatorFactory;
use Drewlabs\Validation\Tests\Stubs\ValidatableModel;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testValidateArray()
    {
        $validator = Validator((new FakeValidatorFactory()));
        $validator = $validator = $validator->validate(
            [
                'x' => 'required',
            ],
            [
                'x' => 100,
            ],
            []
        );

        $this->assertFalse($validator->fails(), 'Expects the validation to fail');
    }

    public function testValidateObject()
    {
        $validator = Validator((new FakeValidatorFactory()));
        $validator = $validator->validate(
            new ValidatableModel(),
            [
                'y' => 100,
            ]
        );
        $this->assertFalse($validator->fails(), 'Expects the validation to fail');
    }

    public function testValidateClass()
    {
        $validator = Validator((new FakeValidatorFactory()));
        $validator = $validator->validate(
            CoreValidatableModel::class,
            [
                'y' => null,
            ]
        );
        $this->assertTrue($validator->fails(), 'Expects the validation to fail');
    }

    public function testValidateUpdateRules()
    {
        $validator = (Validator((new FakeValidatorFactory())))->updating();
        $validator = $validator->validate(
            CoreValidatableModel::class,
            [
                'y' => 100,
            ]
        );
        $this->assertFalse($validator->fails(), 'Expects the validation to fail');
    }

    public function testValidateWithCallback()
    {
        $validator = (Validator((new FakeValidatorFactory())));
        $result = $validator->validate(
            CoreValidatableModel::class,
            [
                'y' => 100,
            ],
            static function () {
                return true;
            }
        );
        $this->assertTrue($result, 'Expect the callback after successful validation to return true');
    }

    public function testValidateWithCallbackThrowException()
    {
        $this->expectException(ValidationException::class);
        $validator = (Validator((new FakeValidatorFactory())));
        $validator->validate(
            CoreValidatableModel::class,
            [
                'y' => null,
            ],
            static function () {
                return true;
            }
        );
    }

    public function testViewModelValidation()
    {
        $this->expectException(ValidationException::class);
        $validator = Validator((new FakeValidatorFactory()));
        $result = $validator->validate(
            (new ValidatableModel())->withBody([
                'y' => null,
                'user_id' => 3,
            ]),
            static function () {
                return true;
            }
        );
        $this->assertTrue($result, 'Expects the validation to fail');
    }

    public function test_before_validation_transformation()
    {
        $validator = Validator((new FakeValidatorFactory()));
        $viewModel = BeforeValidationViewModel::new()->merge([
            'details' => [
                'account_number' => '02401342-924-009',
                'amount' => 45000,
            ],
        ]);
        $result = $validator->validate($viewModel, static function () {
            return true;
        });
        $this->assertTrue($result, 'Expect validation to passed');
    }
}
