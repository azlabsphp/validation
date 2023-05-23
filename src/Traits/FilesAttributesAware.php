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

namespace Drewlabs\Validation\Traits;

/**
 * @template TUploadedFile
 */
trait FilesAttributesAware
{
    /**
     * Uploaded files container object.
     *
     * @var TUploadedFile[]
     */
    private $__FILES__ = [];

    /**
     * Files property getter and setter
     * 
     * @param array|null $files
     * 
     * @return mixed[] 
     */
    public function files(array $files = null)
    {
        if (null !== $files) {
            $this->__FILES__ = \is_array($files) ? $files : [];
        }
        return $this->allFiles();
    }

    /**
     * Append a file to the list of files attached to the current object.
     *
     * @param mixed $file
     *
     * @return self
     */
    public function addFile(string $key, $file)
    {
        if ($file) {
            $this->__FILES__[$key] = $file;
        }
        return $this;
    }

    /**
     * Get a file from the list of attached files.
     * 
     * @template TUploadedFile
     *
     * @return TUploadedFile
     */
    public function file(string $key)
    {
        return $this->__FILES__[$key] ?? null;
    }

    /**
     * Returns the list of files attached to the current object.
     *
     * @template TUploadedFile
     * 
     * @return TUploadedFile[]
     */
    public function allFiles()
    {
        return $this->__FILES__ ?? [];
    }

    /**
     * Determine if the uploaded data contains a file with matching key.
     *
     * @param  string  $key
     * 
     * @return bool
     */
    public function hasFile(string $key)
    {
        return array_key_exists($key, $this->__FILES__);
    }
}
