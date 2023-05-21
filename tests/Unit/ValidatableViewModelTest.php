<?php

use Drewlabs\Contracts\Validator\ViewModel;
use Drewlabs\Validation\Exceptions\ValidationException;
use Drewlabs\Validation\Tests\Stubs\FakeValidatorFactory;
use Drewlabs\Validation\Tests\Stubs\TestViewModel;
use Drewlabs\Validation\ValidatorAdapter;
use PHPUnit\Framework\TestCase;

class ValidatableViewModelTest extends TestCase
{

    public function test_view_model_new_method_creates_a_new_view_model_instance()
    {
        $view = TestViewModel::new([]);
        $this->assertInstanceOf(ViewModel::class, $view);
    }

    public function test_view_model_new_method_add_inputs_to_view_model_instance()
    {

        $view = TestViewModel::new(['prop_1' => 'Hello']);
        $this->assertEquals($view->prop_1, 'Hello');
    }

    public function test_view_model_validate_method_validates_inputs_and_throws_exception_on_error()
    {
        $this->expectException(ValidationException::class);
        $view = TestViewModel::new(['prop_1' => 'Hello']);
        $result = $view->merge(['prop_2' => 'Welcome'])->validate(new ValidatorAdapter(new FakeValidatorFactory), function($view) {
            return $view->prop2;
        });
        $this->assertEquals('Welcome', $result);
    }

    public function test_view_model_validate_method_validates_inputs_and_return_callback_returned_value()
    {
        $view = TestViewModel::new(['prop_1' => '3']);
        $result = $view->merge(['prop_2' => 'Welcome'])->validate(new ValidatorAdapter(new FakeValidatorFactory), function($view) {
            return $view->prop_2;
        });
        $this->assertEquals('Welcome', $result);
    }


    public function test_view_model_validate_method_return_view_model_instance_if_validation_passes()
    {
        $view = TestViewModel::new(['prop_1' => '3', 'prop_2' => 'Welcome']);
        $result = $view->validate(new ValidatorAdapter(new FakeValidatorFactory));
        $this->assertSame($view, $result);

    }

}