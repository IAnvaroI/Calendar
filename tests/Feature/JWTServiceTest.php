<?php

namespace Tests\Feature;

use App\Contracts\JWT;
use App\Services\JWTService;
use Tests\TestCase;

class JWTServiceTest extends TestCase
{
    /**
     * @var JWTService|mixed
     */
    private JWTService $jwtService;

    /**
     * @var string
     */
    private string $token;

    /**
     * @var array
     */
    private array $decodedToken;

    /**
     * @var int
     */
    private int $expTime;

    public function setUp(): void
    {
        parent::setUp();

        $this->jwtService = $this->app->make(JWT::class);
        $this->token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJcdTAwMDAqXHUwMDAwY2xhaW1GYWN0b3J5Ijp7fSwiXHUwMDAwKlx1'
            . 'MDAwMHZhbGlkYXRvciI6e30sIlx1MDAwMCpcdTAwMDBkZWZhdWx0Q2xhaW1zIjpbImlzcyIsImlhdCIsImV4cCIsIm5iZiIsImp0aS'
            . 'JdLCJcdTAwMDAqXHUwMDAwY2xhaW1zIjp7ImFkZENsYWltcyI6eyJleHAiOjE2ODQxMzUyMTB9fSwiXHUwMDAwKlx1MDAwMGN1c3Rv'
            . 'bUNsYWltcyI6W10sIlx1MDAwMCpcdTAwMDByZWZyZXNoRmxvdyI6ZmFsc2V9.isT901mTxNK2t7LYYwCcPD1KYgCWW1Tnw6SQiSvIXwA';
        $this->expTime = 1684135210;
        $this->decodedToken = [
            "claimFactory" => [],
            "validator" => [],
            "defaultClaims" => [
                0 => "iss",
                1 => "iat",
                2 => "exp",
                3 => "nbf",
                4 => "jti",
            ],
            "claims" => [
                "addClaims" => [
                    "exp" => $this->expTime
                ]
            ],
            "customClaims" => [],
            "refreshFlow" => false
        ];
    }

    public function test_encode(): void
    {
        $actualToken = $this->jwtService->encode([
            'exp' => $this->expTime
        ]);

        $this->assertEquals($this->token, $actualToken);
    }

    public function test_decode(): void
    {

        $actualDecodedToken = $this->jwtService->decode($this->token);

        $this->assertEquals($this->decodedToken, $actualDecodedToken);
    }
}
