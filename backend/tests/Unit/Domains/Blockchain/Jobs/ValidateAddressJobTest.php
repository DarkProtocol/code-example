<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Blockchain\Jobs;

use Lucid\Exceptions\InvalidInputException;
use Lucid\Validation\Validator;
use Tests\TestCase;
use App\Domains\Blockchain\Jobs\ValidateAddressJob;

class ValidateAddressJobTest extends TestCase
{
    /** @var array<string, array<string, string>> */
    protected array $validTestSets = [
        'BTC' => [
            'address' => '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa',
        ],
        'ETH' => [
            'address' => '0x000000000000000000000000000000000000dEaD',
        ],
        'LTC' => [
            'L address' => 'LSdTvMHRm8sScqwCi6x9wzYQae8JeZhx6y',
            //'M address' => '',
            // TODO: ltc1 address
        ],
        'XRP' => [
            'address' => 'rEb8TK3gBgk5auZkwc6sHnwrGVJH8DuaLh',
        ],
    ];

    /** @var array<string, array<string, string>> */
    protected array $invalidTestSets = [
        'BTC' => [
            'no address' => '',
            'length <' => '1A1zP1eP5QGefi2DMPTfTL5SL',
            'length >' => '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNaxxxxxxxxxxxxx',
            'does not starts from 1 or 3' => '2A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa',
            'contains O, I, l, or zero' => '1A1zP1eP5QGefi2DMPTfTL5SLmv7DiOIL0',
        ],
        'ETH' => [
            'no address' => '',
            'without 0x' => '000000000000000000000000000000000000dEaD',
            'length <' => '0x000000000000000000000000000000000000dEa',
            'length >' => '0x000000000000000000000000000000000000dEaDD',
            'invalid characters' => '0x000000000000000000000000000000000000HHHH',
        ],
        'LTC' => [
            'no address' => '',
            'length <' => 'LSdTvMHRm8sScqwCi6x9wzYQa',
            'length >' => 'LSdTvMHRm8sScqwCi6x9wzYQae8JeZhx6yxx',
        ],
        'XRP' => [
            'no address' => '',
            'not started with r' => 'xEb8TK3gBgk5auZkwc6sHnwrGVJH8DuaLh',
            'length <' => 'rEb8TK3gBgk5auZkwc6sHnwr',
            'length >' => 'rEb8TK3gBgk5auZkwc6sHnwrGVJH8DuaLhxx',
        ],
    ];

    /**
     * @dataProvider validAddressesProvider
     * @param string $address
     * @param string $symbol
     * @throws InvalidInputException
     */
    public function testValidateAddressJobForValidAddresses(string $address, string $symbol): void
    {
        /** @var Validator $validator */
        $validator = app(Validator::class);

        $job = new ValidateAddressJob($address, $symbol);
        $this->assertTrue($job->handle($validator));
    }

    /**
     * @dataProvider invalidAddressesProvider
     * @param string $address
     * @param string $symbol
     * @throws InvalidInputException
     */
    public function testValidateAddressJobForInvalidAddresses(string $address, string $symbol): void
    {
        /** @var Validator $validator */
        $validator = app(Validator::class);

        $job = new ValidateAddressJob($address, $symbol);
        $this->assertFalse($job->handle($validator));
    }

    /** @return array<string, array<string, string>> */
    public function validAddressesProvider(): array
    {
        $data = [];

        foreach ($this->validTestSets as $algo => $testSet) {
            foreach ($testSet as $testCase => $address) {
                $data[$algo . ' ' . $testCase] = [
                    'address' => $address,
                    'algo' => $algo,
                ];
            }
        }

        return $data;
    }

    /** @return array<string, array<string, string>> */
    public function invalidAddressesProvider(): array
    {
        $data = [];

        foreach ($this->invalidTestSets as $algo => $testSet) {
            foreach ($testSet as $testCase => $address) {
                $data[$algo . ' ' . $testCase] = [
                    'address' => $address,
                    'algo' => $algo,
                ];
            }
        }

        return $data;
    }
}
