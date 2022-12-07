<?php

declare(strict_types=1);

namespace App\Services\Auth\Http\Controllers;

use App\Services\Auth\Features\ChangePasswordFeature;
use Lucid\Units\Controller;

class ChangePasswordController extends Controller
{
    public function __invoke(): mixed
    {
        return $this->serve(ChangePasswordFeature::class);
    }
}
