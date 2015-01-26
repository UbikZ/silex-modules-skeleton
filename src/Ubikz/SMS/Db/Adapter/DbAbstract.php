<?php

namespace Ubikz\SMS\Db\Adapter;

use Doctrine\DBAL\Driver\PDOConnection;

/**
 * Class DbAbstract
 * @package Ubikz\SMS\Db\Adapter
 */
abstract class DbAbstract
{
    /** @var  PDOConnection */
    private $con;

    /** @var  string */
    private $dsn;

    /** @var  string */
    private $user;

    /** @var  string */
    private $password;

    public function __construct($connection)
    {
        $this->parseConnection($connection);
    }

    /**
     * @param $connection
     */
    protected function generateDsn($connection)
    {
        $dsn = [];
        unset($connection['adapter']);
        unset($connection['user']);
        unset($connection['password']);
        foreach ($connection as $key => $value) {
            $dsn[$key] = sprintf('%s=%s', $key, $value);
        }
        $this->setDsn(implode(';', $dsn));
    }

    /**
     * @param $connection
     */
    protected function parseConnection($connection)
    {
        if (!isset($connection['user'])) {
            throw new \PDOException('User database not found.');
        }
        $this->setUser($connection['user']);

        if (!isset($connection['password'])) {
            throw new \PDOException('Password database not found.');
        }
        $this->setPassword($connection['password']);

        $this->generateDsn($connection);
    }

    /**
     * @return string
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @param string $dsn
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return PDOConnection
     */
    public function getCon()
    {
        return $this->con;
    }

    /**
     * @param PDOConnection $con
     */
    public function setCon($con)
    {
        $this->con = $con;
    }
}
