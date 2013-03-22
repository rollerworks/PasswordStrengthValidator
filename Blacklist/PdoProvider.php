<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Blacklist;

/**
 * Sqlite Blacklist Provider.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class PdoProvider implements ImmutableBlacklistProviderInterface
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

    /**
     * {@inheritdoc}
     */
    public function write($password)
    {
        $db = $this->initDb();
        $args = array(
            ':password' => $password,
            ':created_at' => time(),
        );

        try {
            if (!$this->isBlacklisted($password)) {
                $this->exec($db, 'INSERT INTO rollerworks_passwd_blacklist (passwd, created_at) VALUES (:password, :created_at)', $args);
            }
            $status = true;
        } catch (\Exception $e) {
            $status = false;
        }

        $this->close($db);

        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $db = $this->initDb();
        $this->exec($db, 'DELETE FROM rollerworks_passwd_blacklist');
        $this->close($db);
    }

    /**
     * {@inheritdoc}
     */
    public function isBlacklisted($password)
    {
        $db = $this->initDb();
        $tokenExists = $this->fetch($db, 'SELECT 1 FROM rollerworks_passwd_blacklist WHERE passwd = :password LIMIT 1', array(':password' => $password));
        $this->close($db);

        return !empty($tokenExists);
    }

    /**
     * Initializes the database
     *
     * @throws \RuntimeException When the requested database driver is not installed
     */
    abstract protected function initDb();

    protected function exec($db, $query, array $args = array())
    {
        $stmt = $this->prepareStatement($db, $query);

        foreach ($args as $arg => $val) {
            $stmt->bindValue($arg, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $success = $stmt->execute();
        if (!$success) {
            throw new \RuntimeException(sprintf('Error executing query "%s"', $query));
        }
    }

    protected function prepareStatement($db, $query)
    {
        try {
            $stmt = $db->prepare($query);
        } catch (\Exception $e) {
            $stmt = false;
        }

        if (false === $stmt) {
            throw new \RuntimeException('The database cannot successfully prepare the statement');
        }

        return $stmt;
    }
}
