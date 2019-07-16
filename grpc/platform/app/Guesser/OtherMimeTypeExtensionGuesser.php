<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Guesser;

use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

/**
 * Provides a best-guess mapping of mime type to file extension.
 */
class OtherMimeTypeExtensionGuesser implements ExtensionGuesserInterface
{
    /**
     * A map of mime types and their default extensions.
     *
     * This list has been placed under the public domain by the Apache HTTPD project.
     * This list has been updated from upstream on 2013-04-23.
     *
     */
    protected $defaultExtensions = [
        'text/x-python' => 'txt',
        'text/x-c' => 'txt'
    ];

    /**
     * {@inheritdoc}
     */
    public function guess($mimeType)
    {
        return isset($this->defaultExtensions[$mimeType]) ? $this->defaultExtensions[$mimeType] : null;
    }
}