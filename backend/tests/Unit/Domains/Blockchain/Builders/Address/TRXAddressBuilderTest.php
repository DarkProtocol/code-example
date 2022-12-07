<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Blockchain\Builders\Address;

use App\Data\Dto\AddressDto;
use App\Domains\Blockchain\Builders\Address\TRXAddressBuilder;
use Tests\TestCase;
use Tuupola\Base58;

class TRXAddressBuilderTest extends TestCase
{
    public function testTRXAddressBuilder(): void
    {
        $g = new TRXAddressBuilder();
        $result = $g->getAddress();

        $this->assertInstanceOf(AddressDto::class, $result);
        $this->assertIsString($result->address);
        $this->assertStringStartsWith('T', $result->address);
        $this->assertMatchesRegularExpression('/^[' . Base58::BITCOIN . ']+$/', $result->address);
        $this->assertIsString($result->privateKey);
        $this->assertTrue(strlen($result->privateKey) === 64);
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $result->privateKey);
    }
}
