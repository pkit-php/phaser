<?php

namespace Phaser\Database;

use Phaser\Abstracts\Connection;
use Phutilities\Env;

class EnvConnection extends Connection
{
    private static array
        $config = [];
    private static ?string
        $user = null,
        $pass = null;

    public static function config(array $config, string $user, string $pass)
    {
        self::$config = $config;
        self::$user = $user;
        self::$pass = $pass;
    }

    private static function getAttribute(string $attribute): string
    {
        if (is_null(self::$config[$attribute])) {
            self::${$attribute} = self::$config[$attribute]
                ?? Env::getEnvOrValue("DB_" . strtoupper($attribute), "");
        }
        return self::$config[$attribute];
    }

    public function getDriver(): string
    {
        if (is_null(self::$config['driver'])) {
            self::$config['driver'] = Env::getEnvOrValue("DB_DRIVER", "mysql");
        }
        return self::$config['driver'];
    }

    public function getUser(): string
    {
        if (is_null(self::$user)) {
            self::$user = Env::getEnvOrValue("DB_USER", "root");
        }
        return self::$user;
    }

    public function getPass(): string
    {
        if (is_null(self::$pass)) {
            self::$pass = Env::getEnvOrValue("DB_PASS", "");
        }
        return self::$pass;
    }

    public function getConfig(): array
    {
        $config = [];
        foreach (self::KEYS as $key) {
            $value = self::getAttribute($key);
            if (strlen($value))
                $config[$key] = $value;
        }
        return $config;
    }
}
