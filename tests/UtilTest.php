<?php
namespace ParagonIE\CipherSweet\Tests;

use ParagonIE\CipherSweet\Util;
use ParagonIE\ConstantTime\Hex;
use PHPUnit\Framework\TestCase;

/**
 * Class UtilTest
 * @package ParagonIE\CipherSweet\Tests
 */
class UtilTest extends TestCase
{
    public function testAes256Ctr()
    {
        $key = \random_bytes(32);
        $nonce = \random_bytes(16);
        for ($i = 0; $i < 10; ++$i) {
            $message = \random_bytes(16 << $i);
            $expected = \openssl_encrypt(
                $message,
                'aes-256-ctr',
                $key,
                OPENSSL_RAW_DATA,
                $nonce
            );
            $actual = Util::aes256ctr($message, $key, $nonce);
            $this->assertSame(
                Hex::encode($expected),
                Hex::encode($actual)
            );
        }
    }

    public function testCtrNonceIncrease()
    {
        $testCases = [
            [
                '00000000000000000000000000000001',
                '00000000000000000000000000000000'
            ],
            [
                '00000000000000000000000000000100',
                '000000000000000000000000000000ff'
            ],
            [
                '0000000000000000000000000000ff00',
                '0000000000000000000000000000feff'
            ],
            [
                '00000000000000000000000000000000',
                'ffffffffffffffffffffffffffffffff'
            ]
        ];
        foreach ($testCases as $testCase) {
            list ($output, $input) = $testCase;
            $this->assertSame(
                $output,
                Hex::encode(Util::ctrNonceIncrease(Hex::decode($input)))
            );
        }
    }
}