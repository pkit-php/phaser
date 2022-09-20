<?php

namespace Phaser;

use \PDO;
use Phaser\Abstracts\Connection;

class Database
{
    protected ?PDO $pdo = null;
    public Connection $connection;

    public function __construct(?Connection $connection = null)
    {
        $this->connection = $connection ?? new EnvConnection;
        $this->setConnection();
    }

    private function setConnection()
    {
        $driver = $this->connection->getDriver();
        $config = $this->connection->getFormattedConfig();
        $this->pdo = new PDO(
            "$driver:$config",
            $this->connection->getUser(),
            $this->connection->getPass()
        );
        // throw exceptions, when SQL error is caused
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // prevent emulation of prepared statements
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function execute(string $query, $params = []): \PDOStatement|false
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
