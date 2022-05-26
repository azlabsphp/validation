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

/**
 * @internal Uses internally by {@see ViewModel} trait
 * **Note**
 * Do not rely on it name as it's inteded to change
 */
trait HasFileAttributes
{
    /**
     * Uploaded files container object.
     *
     * @var array
     */
    private $files_ = [];

    /**
     * Attache a list of files to the current object.
     *
     * @return self|array
     */
    public function files(array $files = [])
    {
        if (null === $files) {
            return $this->allFiles();
        }
        $this->files_ = \is_array($files) ? $files : [];

        return $this;
    }

    /**
     * Append a file to the list of files attached to the current object.
     *
     * @param mixed $file
     *
     * @return self
     */
    public function addFile(string $name, $file)
    {
        if ($file) {
            $this->files_[$name] = $file;
        }

        return $this;
    }

    /**
     * Get a file from the list of attached files.
     *
     * @return mixed|UploadedFile
     */
    public function file(string $key)
    {
        return $this->files_[$key] ?? null;
    }

    /**
     * Returns the list of files attached to the current object.
     *
     * @return array|UploadedFile[]
     */
    public function allFiles()
    {
        return $this->files_ ?? [];
    }
}
