<?php

declare(strict_types=1);

namespace App\Services\Auth\Operations;

use App\Data\Models\User;
use App\Domains\Auth\Jobs\IssueAccessTokenJob;
use App\Domains\Auth\Jobs\IssueRefreshTokenJob;
use App\Domains\Http\Jobs\DetectRequestCountryJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Lucid\Units\Operation;

class IssueTokensOperation extends Operation
{
    /**
     * Create a new operation instance.
     *
     * @return void
     */
    public function __construct(
        protected User $user,
        protected string $action,
        protected User $createdBy,
        protected bool $withResponse = true
    ) {
    }

    /**
     * Execute the operation.
     *
     * @return JsonResponse|null
     */
    public function handle(): ?JsonResponse
    {
        $sessionId = Str::orderedUuid()->toString();

        $requestCountry = $this->run(DetectRequestCountryJob::class, [
            'detectTor' => true,
        ]);

        $this->run(IssueAccessTokenJob::class, [
            'user' => $this->user,
            'sessionId' => $sessionId,
            'requestCountry' => $requestCountry,
            'action' => $this->action,
            'createdBy' => $this->createdBy,
        ]);

        $this->run(IssueRefreshTokenJob::class, [
            'user' => $this->user,
            'sessionId' => $sessionId,
            'requestCountry' => $requestCountry,
            'action' => $this->action,
            'createdBy' => $this->createdBy,
        ]);

        return $this->withResponse ? response()->json(null, 204) : null;
    }
}
