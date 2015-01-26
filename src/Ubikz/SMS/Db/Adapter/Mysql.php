<?php

namespace Ubikz\SMS\Db\Adapter;

use Doctrine\DBAL\Driver\PDOConnection;

/**
 * Class Pdo
 * @package Ubikz\SMS\Db\Adapter
 */
class Mysql extends DbAbstract
{
    /**
     * @param $connection
     */
    public function __construct($connection)
    {
        parent::__construct($connection);
        $this->setCon(new PDOConnection($this->getDsn(), $this->getUser(), $this->getPassword()));
    }

    protected function generateDsn($connection)
    {
        parent::generateDsn($connection);
        $this->setDsn(sprintf('mysql:%s', $this->getDsn()));
    }
}
