<?php

namespace App\Interfaces;

interface AuthInterface
{
    /**
     * Получение токена досутпа для аккаунта
     *
     * @param array $queryParams Входные GET параметры.
     * @return string | array  Имя авторизованного аккаунта | Вывод ошибки
     */
    public function auth(array $queryParams): array|string;
}
