<?php

namespace App\Contracts;

interface JWT
{
    /**
     * @param array $claims
     * @return string
     */
    public function encode(array $claims = []): string;

    /**
     * @param string $token
     *
     * @return array
     */
    public function decode(string $token): array;
}
