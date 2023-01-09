<?php

namespace Phaser;

use Phutilities\Sanitize;
use PDO;
use Phaser\Abstracts\Connection;
use ReflectionProperty as PrivateProperty;

class Table
{
  private string $_table = "";
  private Database $_database;

  public function __construct(array $properties = [], Connection $connection = new EnvConnection)
  {
    foreach ($properties as $key => $value) {
      $this->{$key} = $value;
    }
    try {
      $this->_table = (new PrivateProperty($this, "table"))
        ->getValue($this);
    } catch (\Throwable $_) {
      $this->_table = Sanitize::class(get_class($this));
    }
    $this->_database = new Database($connection);
  }

  public function __set($prop, $value)
  {
    if (str_starts_with($prop, "_")) {
      throw new \Exception("A propriedade $prop não pode ser definida, pois é privada", 500);
    }
    return $this->$prop = $value;
  }

  public function __get($prop)
  {
    if (str_starts_with($prop, "_")) {
      throw new \Exception("A propriedade $prop não pode ser retornada, pois é privada", 500);
    }
    return $this->$prop;
  }

  public function insert(?array $returns = null)
  {
    $array = Sanitize::objectProperties($this);
    $array = array_filter($array);

    $query = (new QueryBuilder($this->_table))->insert($array);

    if ($returns) {
      $query->return($returns);
    }

    $stmt = $this->_database->execute($query, $query->getParams());

    return $stmt->fetch();
  }

  public function count(array $where = null)
  {
    $query = (new QueryBuilder($this->_table))->select(["COUNT(*)"]);

    if ($where) {
      $query->where($where);
    }

    $stmt = $this->_database->execute($query, $query->getParams());

    return $stmt->fetch()[0];
  }

  public function select(array $where = null, array $orderBy = null, array $limit = null)
  {
    $array = Sanitize::objectProperties($this);
    $keys = array_keys($array);

    $query = (new QueryBuilder($this->_table))->select($keys);

    if ($where) {
      $query->where($where);
    }
    if ($orderBy) {
      $query->order($orderBy);
    }
    if ($limit) {
      $query->limit($limit);
    }

    $stmt = $this->_database->execute($query, $query->getParams());

    return $stmt->fetchAll(PDO::FETCH_CLASS, get_class($this));
  }

  public function update(array $where = null)
  {
    $array = Sanitize::objectProperties($this);
    $array = array_filter($array);

    $query = (new QueryBuilder($this->_table))->update($array);

    if ($where) {
      $query->where($where);
    }

    $this->_database->execute($query, $query->getParams());
  }

  public function delete(array $where = null)
  {
    $query = (new QueryBuilder($this->_table))->delete();

    if ($where) {
      $query->where($where);
    }

    $this->_database->execute($query, $query->getParams());
  }
}
