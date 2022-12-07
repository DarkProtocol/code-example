<?php

declare(strict_types=1);

use App\Domains\Blockchain\Enums\BTCNetworkType;
use App\Domains\Blockchain\Enums\ETHNetworkType;
use App\Domains\Blockchain\Enums\NetworkType;

return [
    'BSC' => [
        'rpc' => env('BLOCKCHAIN_BSC_RPC', env('NODE_RPC_BSC')),
        'network' => NetworkType::from(env('BLOCKCHAIN_BSC_NETWORK', NetworkType::Mainnet->value)),
    ],
    'BTC' => [
        'rpc' => env('BLOCKCHAIN_BTC_RPC', env('NODE_RPC_BTC')),
        'network' => BTCNetworkType::from(env('BLOCKCHAIN_BTC_NETWORK', BTCNetworkType::Mainnet->value)),
    ],
    'ETH' => [
        'rpc' => env('BLOCKCHAIN_ETH_RPC', env('NODE_RPC_ETH')),
        'network' => ETHNetworkType::from(env('BLOCKCHAIN_ETH_NETWORK', ETHNetworkType::Mainnet->value)),
    ],
    'LTC' => [
        'rpc' => env('BLOCKCHAIN_LTC_RPC', env('NODE_RPC_LTC')),
        'network' => NetworkType::from(env('BLOCKCHAIN_LTC_NETWORK', NetworkType::Mainnet->value)),
    ],
    'TRX' => [
        'rpc' => env('BLOCKCHAIN_TRX_RPC', env('NODE_RPC_TRX')),
        'rpcLib' => env('NODE_RPC_TRX_LIB'),
        'apiKey' => env('NODE_RPC_TRX_API_KEY'),
        'network' => NetworkType::from(env('BLOCKCHAIN_TRX_NETWORK', NetworkType::Mainnet->value)),
    ],
];
