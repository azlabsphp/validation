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

use Drewlabs\Contracts\Validator\BaseValidatable;
use function Drewlabs\Validation\Proxy\ViewModel;
use Drewlabs\Validation\Tests\Stubs\AuthenticatableStub;
use Drewlabs\Validation\Tests\Stubs\TestViewModel;
use PHPUnit\Framework\TestCase;

class AttributeViewModelTest extends TestCase
{
    public function testViewModelCreator()
    {
        $viewModel = ViewModel(new TestViewModel());
        $viewModel2 = ViewModel(new TestViewModel(), new AuthenticatableStub());
        $viewModel3 = ViewModel(new TestViewModel(), new AuthenticatableStub(), [
            'firstname' => 'Azandrew',
            'lastname' => 'Sidoine',
        ], []);
        $this->assertInstanceOf(BaseValidatable::class, $viewModel, 'Expect the result of the ViewModel function to be an instance of ' . BaseValidatable::class);
        $this->assertInstanceOf(BaseValidatable::class, $viewModel2, 'Expect the result of the ViewModel function to be an instance of ' . BaseValidatable::class);
        $this->assertInstanceOf(BaseValidatable::class, $viewModel3, 'Expect the result of the ViewModel function to be an instance of ' . BaseValidatable::class);
    }

    public function testTestViewModel()
    {
        $viewModel = ViewModel(new TestViewModel(), new AuthenticatableStub(), [
            'firstname' => 'Azandrew',
            'lastname' => 'Sidoine',
            'address' => [
                'email' => 'azandrewdevelopper@gmail.com',
            ],
        ], ['file1' => new \stdClass()]);
        $this->assertSame('Azandrew', $viewModel->get('firstname'), 'Expect the user provided firstname to equals Azandrew');
        $this->assertSame('azandrewdevelopper@gmail.com', $viewModel->get('address.email'), 'Expect the user provided email to equal azandrewdevelopper@gmail.com');
        $this->assertIsArray($viewModel->all(), 'Expect the all method to return an array');

        // Test loading files from the view model
        $this->assertInstanceOf(\stdClass::class, $viewModel->file('file1'), 'Expect the file method to return an uploaded file object');
        $this->assertNull($viewModel->file('file2'), 'Expect the file2 to not be in the list of files attached to the view model');
        $viewModel->addFile('file2', new \stdClass());
        $this->assertInstanceOf(\stdClass::class, $viewModel->file('file2'), 'Expect the file() method to return an uploaded file object with filename file2');
    }
}
