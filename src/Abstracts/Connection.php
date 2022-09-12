<?php

namespace Phaser\Abstracts;

abstract class Connection
{
    public const KEYS = [
        "dbname",
        "host",
        "port",
        "charset",
        "dialect"
    ];

    abstract public function getDriver(): string;

    abstract public function getUser(): string;

    abstract public function getPass(): string;

    abstract public function getConfig(): array;

    public function getFormattedConfig(): string
    {
        $configArr = $this->getConfig();
        $config = "";
        foreach ($configArr as $key => $value) {
            if (is_numeric($key))
                $config .= $value;
            else
                if (strlen($value))
                    $config .= "$key=$value;";
        }
        return $config;
    }
}
