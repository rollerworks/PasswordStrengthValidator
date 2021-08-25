<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Blacklist;

/**
 * Sqlite Blacklist Provider.
 */
abstract class PdoProvider implements UpdatableBlacklistProviderInterface
{
    protected $dsn;
    protected $username;
    protected $password;
    protected $db;

    /**
     * Constructor.
     *
     * @param string $dsn      A data source name
     * @param string $username The username for the database
     * @param string $password The password for the database
     */
    public function __construct($dsn, $username = '', $password = '')
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    public function add($password)
    {
        if (! is_scalar($password)) {
            throw new \InvalidArgumentException('Only scalar values are accepted.');
        }

        if ($password === '') {
            return -1;
        }

        $db = $this->initDb();
        $args = [
            ':password' => $password,
            ':created_at' => time(),
        ];

        try {
            if ($this->isBlacklisted($password)) {
                $status = -1;
            } else {
                $this->exec($db, 'INSERT INTO rollerworks_passdbl (passwd, created_at) VALUES (:password, :created_at)', $args);
                $status = true;
            }
        } catch (\Exception $e) {
            $status = false;
        }

        if (! $status) {
            $this->close($db);
        }

        return $status;
    }

    public function delete($password)
    {
        if (! is_scalar($password)) {
            throw new \InvalidArgumentException('Only scalar values are accepted.');
        }

        $db = $this->initDb();
        $args = [
            ':password' => $password,
        ];

        try {
            $this->exec($db, 'DELETE FROM rollerworks_passdbl WHERE passwd = :password', $args);
            $status = true;
        } catch (\Exception $e) {
            $status = false;
        }

        if (! $status) {
            $this->close($db);
        }

        return $status;
    }

    public function all()
    {
        $db = $this->initDb();

        return $this->exec($db, 'SELECT passwd FROM rollerworks_passdbl');
    }

    public function purge()
    {
        $db = $this->initDb();
        $this->exec($db, 'DELETE FROM rollerworks_passdbl');
        $this->close($db);
    }

    public function isBlacklisted($password)
    {
        if (! is_scalar($password)) {
            throw new \InvalidArgumentException('Only scalar values are accepted.');
        }

        $db = $this->initDb();
        $tokenExists = $this->fetch($db, 'SELECT 1 FROM rollerworks_passdbl WHERE passwd = :password LIMIT 1', [':password' => $password]);

        return ! empty($tokenExists);
    }

    /**
     * Initializes the database.
     *
     * @throws \RuntimeException When the requested database driver is not installed
     */
    abstract protected function initDb();

    /**
     * @param object $db
     * @param string $query
     */
    protected function fetch($db, $query, array $args = [])
    {
        $stmt = $this->prepareStatement($db, $query);

        foreach ($args as $arg => $val) {
            $stmt->bindValue($arg, $val, \is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param object $db
     * @param string $query
     *
     * @throws \RuntimeException
     */
    protected function exec($db, $query, array $args = [])
    {
        $stmt = $this->prepareStatement($db, $query);

        foreach ($args as $arg => $val) {
            $stmt->bindValue($arg, $val, \is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $success = $stmt->execute();

        if (! $success) {
            throw new \RuntimeException(sprintf('Error executing query "%s".', $query));
        }
    }

    /**
     * @param object $db
     * @param string $query
     *
     * @throws \RuntimeException
     *
     * @return bool|\PDOStatement|\SQLite3Stmt
     */
    protected function prepareStatement($db, $query)
    {
        try {
            $stmt = $db->prepare($query);
        } catch (\Exception $e) {
            $stmt = false;
        }

        if ($stmt === false) {
            throw new \RuntimeException('The database cannot successfully prepare the statement.');
        }

        return $stmt;
    }
}
