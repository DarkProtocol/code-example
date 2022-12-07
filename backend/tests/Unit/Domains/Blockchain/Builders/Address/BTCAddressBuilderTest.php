<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Blockchain\Builders\Address;

use App\Data\Dto\AddressDto;
use App\Domains\Blockchain\Builders\Address\BTCAddressBuilder;
use App\Domains\Blockchain\Enums\BTCNetworkType;
use Tests\TestCase;

class BTCAddressBuilderTest extends TestCase
{
    public function testBTCAddressBuilder(): void
    {
        $g = new BTCAddressBuilder();
        $g->network(BTCNetworkType::Mainnet);
        $result = $g->getAddress();

        $this->assertInstanceOf(AddressDto::class, $result);
        $this->assertIsString($result->address);
        //$this->assertMatchesRegularExpression('/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$/', $result->address);
        $this->assertMatchesRegularExpression('/^bc1[a-z0-9]{39,59}$/', $result->address);
        $this->assertIsString($result->privateKey);
        // TODO: Private key test
    }
}
