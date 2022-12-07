<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Blockchain\Builders\Address;

use App\Data\Dto\AddressDto;
use App\Domains\Blockchain\Builders\Address\LTCAddressBuilder;
use App\Domains\Blockchain\Enums\NetworkType;
use Tests\TestCase;

class LTCAddressBuilderTest extends TestCase
{
    public function testLTCAddressBuilder(): void
    {
        $g = new LTCAddressBuilder();
        $g->network(NetworkType::Mainnet);
        $result = $g->getAddress();

        $this->assertInstanceOf(AddressDto::class, $result);
        $this->assertIsString($result->address);
        //$this->assertMatchesRegularExpression('/^L[a-km-zA-HJ-NP-Z1-9]{26,33}$/', $result->address);
        $this->assertMatchesRegularExpression('/^ltc1[a-z0-9]{39,59}$/', $result->address);
        $this->assertIsString($result->privateKey);
        // TODO: Private key test
    }
}
