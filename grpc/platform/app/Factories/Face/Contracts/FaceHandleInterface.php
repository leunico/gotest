<?php

namespace App\Factories\Face\Contracts;

/**
 * Interface FaceHandleInterface.
 *
 * @author lizx
 */
interface FaceHandleInterface
{
    /**
     * @param $imageFile
     */
    public function detect($imageFile): array;

    /**
     * @param string $faceToken
     * @param string $photo
     */
    public function compare(string $faceToken, string $photo): array;
}
