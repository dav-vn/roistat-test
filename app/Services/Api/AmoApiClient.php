<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Log;
use AmoCRM\Client\AmoCRMApiClient;

/**
 * Class AmoApiService
 *
 * @package App\Services
 */
class AmoApiClient
{
    /**
     * @var AmoCRMApiClient AmoCRM client.
     */
    protected AmoCRMApiClient $baseClient;

    /**
     * AmoApiService constructor.
     */
    public function __construct()
    {
        Log::info('Configuring amoCRM Integration');

        $this->baseClient = new AmoCRMApiClient(
            env('INTEGRATION_CLIENT_ID'),
            env('INTEGRATION_CLIENT_SECRET'),
            env('INTEGRATION_REDIRECT_URL')
        );
    }
}
