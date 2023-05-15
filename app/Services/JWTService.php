<?php

namespace App\Services;

use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\JWT as JWTProvider;
use PHPOpenSourceSaver\JWTAuth\Factory;
use App\Contracts\JWT;

class JWTService implements JWT
{
    /**
     * @var Factory
     */
    private Factory $payloadFactory;

    /**
     * @var JWTProvider
     */
    private JWTProvider $provider;

    /**
     * @param JWTProvider $provider
     * @param Factory $payloadFactory
     */
    public function __construct(JWTProvider $provider, Factory $payloadFactory)
    {
        $this->provider = $provider;
        $this->payloadFactory = $payloadFactory;
    }

    /**
     * @param array $claims
     * @return string
     */
    public function encode(array $claims = []): string
    {
        return $this->provider->encode(
            (array)$this->payloadFactory->addClaims($claims)
        );
    }

    /**
     * @param string $token
     * @return array
     */
    public function decode(string $token): array
    {
        return $this->fixArrayKeys($this->provider->decode($token));
    }

    /**
     * @param $array
     * @return array
     */
    private function fixArrayKeys($array): array
    {
        $fixedArray = [];
        foreach ($array as $key => $value) {
            $key = str_replace("\x00*\x00", '', $key);
            $fixedArray[$key] = $value;
        }

        return $fixedArray;
    }
}
