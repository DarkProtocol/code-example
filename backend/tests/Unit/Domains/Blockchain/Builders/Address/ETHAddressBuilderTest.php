<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Blockchain\Builders\Address;

use App\Data\Dto\AddressDto;
use App\Domains\Blockchain\Builders\Address\ETHAddressBuilder;
use Tests\TestCase;

class ETHAddressBuilderTest extends TestCase
{
    public function testETHAddressBuilder(): void
    {
        $g = new ETHAddressBuilder();
        $result = $g->getAddress();

        $this->assertInstanceOf(AddressDto::class, $result);
        $this->assertIsString($result->address);
        $this->assertStringStartsWith('0x', $result->address);
        $this->assertTrue(strlen($result->address) === 42);
        $this->assertMatchesRegularExpression('/^0x[a-fA-F0-9]{40}$/', $result->address);
        $this->assertIsString($result->privateKey);
        $this->assertStringStartsWith('0x', $result->privateKey);
        $this->assertTrue(strlen($result->privateKey) === 66);
        $this->assertMatchesRegularExpression('/^0x[a-f0-9]+$/', $result->privateKey);
    }
}
