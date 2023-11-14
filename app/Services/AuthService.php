<?php

namespace App\Services;

use AmoCRM\Exceptions\BadTypeException;
use App\Models\Account;
use App\Services\Api\AmoApiClient;
use Carbon\Carbon;
use Exception;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use App\Interfaces\AuthInterface;
use App\Models\Access;
use Throwable;

session_start();

/**
 * Class AuthService
 *
 * @package App\Services
 */
class AuthService extends AmoApiClient implements AuthInterface
{
    /** @var string TARGET_DOMAIN базовый домен */
    private const TARGET_DOMAIN = 'kommo.com';

    /**
     * Аутентификация пользователя в системе
     *
     * @param array $queryParams
     * @return string|array
     * @throws BadTypeException
     * @throws Exception
     */
    public function auth(array $queryParams): array|string
    {
        if (!empty($queryParams['id'])) {
            $_SESSION['service_id'] = $queryParams['id'];
        }

        $hasAccess = Access::where('amo_id', $_SESSION['service_id'])->first();

        if (!empty($hasAccess)) {
            $accessToken = $this->initialise(intval($_SESSION['service_id']));
            if (!$accessToken->hasExpired()) {
                return $this
                    ->baseClient
                    ->getOAuthClient()
                    ->getResourceOwner($accessToken)
                    ->getName();
            }
        }

        if (isset($queryParams['referer'])) {
            $this->baseClient->setAccountBaseDomain($queryParams['referer'])
                ->getOAuthClient()->setBaseDomain($queryParams['referer']);
        }

        if (!isset($queryParams['code'])) {
            $state = bin2hex(random_bytes(16));
            $_SESSION['oauth2state'] = $state;

            if (isset($queryParams['button'])) {
                echo $this->baseClient->getOAuthClient()
                    ->setBaseDomain(self::TARGET_DOMAIN)
                    ->getOAuthButton(
                        [
                            'title' => 'Установить интеграцию',
                            'compact' => true,
                            'class_name' => 'className',
                            'color' => 'default',
                            'error_callback' => 'handleOauthError',
                            'state' => $state
                        ]
                    );
            } else {
                $authorizationUrl = $this->baseClient->getOAuthClient()
                    ->setBaseDomain(self::TARGET_DOMAIN)
                    ->getAuthorizeUrl(['state' => $state, 'mode' => 'post_message']);
                header('Location:' . $authorizationUrl);
            }
            exit;
        } elseif (empty($queryParams['state']) ||
            empty($_SESSION['oauth2state']) ||
            ($queryParams['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            throw new Exception('Invalid state');
        }

        try {
            $accessToken = $this->baseClient->getOAuthClient()
                ->setBaseDomain($queryParams['referer'])
                ->getAccessTokenByCode($queryParams['code']);

            $this->saveToken($_SESSION['service_id'], [
                'base_domain' => $this->baseClient->getAccountBaseDomain(),
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
            ]);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }

        session_abort();

        return $this
            ->baseClient
            ->getOAuthClient()
            ->getResourceOwner($accessToken)
            ->getName();
    }

    /**
     * Инициализация сессии пользователя
     *
     * @param $userId
     * @return AccessToken
     * @throws Exception
     */
    public function initialise($userId): AccessToken
    {
        $accessToken = $this->readToken($userId);

        $this->baseClient->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['base_domain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) use ($userId) {
                    $this->saveToken(intval($userId), [
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $baseDomain,
                    ]);
                }
            );

        return $accessToken;
    }


    /**
     * Read token from the database table (accesses)
     *
     * @param int $userID
     * @return AccessToken
     * @throws Exception
     */
    public function readToken(int $userID): AccessToken
    {
        try {
            $accessToken = Access::on()->where('amo_id', $userID)->first();
            if (!$accessToken) {
                throw new Exception('Access token not found.');
            }
            return new AccessToken($accessToken->toArray());
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Добавление токена в таблицу БД (accesses)
     *
     * @param int $userId
     * @param array $token
     * @return void
     */
    public function saveToken(int $userId, array $token): void
    {
        if (!empty($token)) {
            Account::updateOrCreate(
                [
                    'amo_id' => $userId,
                ],
            );
            Access::updateOrCreate([
                'amo_id' => $userId,
            ], [
                'base_domain' => $token['base_domain'],
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'],
                'expires' => $token['expires'],
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
