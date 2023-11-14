<?php

namespace App\Services;

use App\Services\Api\AmoApiClient;
use Throwable;
use App\Interfaces\AuthInterface;

/**
 * Class SimpleAuthService
 *
 * @package App\Services
 */
class SimpleAuthService extends AmoApiClient implements AuthInterface
{
    /**
     * Get access token for the account when authorization code is present
     *
     * @param array $queryParams Input GET parameters
     * @return array|string Name of the authorized account | Error message
     */
    public function auth(array $queryParams): array|string
    {
        try {
            $this
                ->baseClient
                ->setAccountBaseDomain($queryParams['referer'])
                ->getOAuthClient()
                ->setBaseDomain($queryParams['referer']);

            $accessToken = $this
                ->baseClient
                ->getOAuthClient()
                ->setBaseDomain($queryParams['referer'])
                ->getAccessTokenByCode($queryParams['code']);
        } catch (Throwable $e) {
            die($e->getMessage());
        }

        session_abort();

        return $this
            ->baseClient
            ->getOAuthClient()
            ->getResourceOwner($accessToken)
            ->getName();
    }
}
