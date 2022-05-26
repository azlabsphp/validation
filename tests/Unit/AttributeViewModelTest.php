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

namespace Drewlabs\Validator\Tests\Unit;

use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Validator\CoreValidatable;
use function Drewlabs\Validator\Proxy\ViewModel;
use Drewlabs\Validator\Tests\Stubs\AuthenticatableStub;
use Drewlabs\Validator\Tests\Stubs\TestViewModel;

use Drewlabs\Validator\Tests\TestCase;

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
        $this->assertInstanceOf(CoreValidatable::class, $viewModel, 'Expect the result of the ViewModel function to be an instance of '.CoreValidatable::class);
        $this->assertInstanceOf(CoreValidatable::class, $viewModel2, 'Expect the result of the ViewModel function to be an instance of '.CoreValidatable::class);
        $this->assertInstanceOf(CoreValidatable::class, $viewModel3, 'Expect the result of the ViewModel function to be an instance of '.CoreValidatable::class);
    }

    public function testTestViewModel()
    {
        $viewModel = ViewModel(new TestViewModel(), new AuthenticatableStub(), [
            'firstname' => 'Azandrew',
            'lastname' => 'Sidoine',
            'address' => [
                'email' => 'azandrewdevelopper@gmail.com',
            ],
        ], [
            'file1' => new \stdClass(),
        ]);
        $this->assertInstanceOf(Authenticatable::class, $viewModel->user(), 'Expect the view model user() method to return an instance of '.Authenticatable::class);
        $this->assertSame('Azandrew', $viewModel->get('firstname'), 'Expect the user provided firstname to equals Azandrew');
        $this->assertSame('azandrewdevelopper@gmail.com', $viewModel->get('address.email'), 'Expect the user provided email to equal azandrewdevelopper@gmail.com');
        $this->assertIsArray($viewModel->all(), 'Expect the all method to return an array');

        // Test loading files from the view model
        $this->assertInstanceOf(\stdClass::class, $viewModel->file('file1'), 'Expect the file method to return an uploaded file object');
        $this->assertNull($viewModel->file('file2'), 'Expect the file2 to not be in the list of files attached to the view model');
        $viewModel->addFile('file2', new \stdClass());
        $this->assertInstanceOf(\stdClass::class, $viewModel->file('file2'), 'Expect the file() method to return an uploaded file object with filename file2');

        // Test set resolver method
        $viewModel->setUserResolver(static function () {
            return null;
        });
        $this->assertNull($viewModel->user(), 'Expect the view model user() method to return null ');
    }
}
