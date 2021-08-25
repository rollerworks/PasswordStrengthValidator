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
class SqliteProvider extends PdoProvider
{
    public function all()
    {
        $db = $this->initDb();

        if ($db instanceof \SQLite3) {
            return $this->fetch($db, 'SELECT passwd FROM rollerworks_passdbl');
        }

        return $this->exec($db, 'SELECT passwd FROM rollerworks_passdbl');
    }

    public function close($db)
    {
        if ($db instanceof \SQLite3) {
            $db->close();
            $this->db = null;
        }
    }

    /**
     * @throws \RuntimeException When neither of SQLite3 or PDO_SQLite extension is enabled
     */
    protected function initDb()
    {
        if ($this->db === null || $this->db instanceof \SQLite3) {
            if (mb_strpos($this->dsn, 'sqlite') !== 0) {
                throw new \RuntimeException(sprintf('Please check your configuration. You are trying to use Sqlite with an invalid dsn "%s". The expected format is "sqlite:/path/to/the/db/file".', $this->dsn));
            }

            if (class_exists('SQLite3')) {
                $db = new \SQLite3(mb_substr($this->dsn, 7, \mb_strlen($this->dsn)), \SQLITE3_OPEN_READWRITE | \SQLITE3_OPEN_CREATE);
                $db->busyTimeout(1000);
            } elseif (class_exists('PDO') && \in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
                $db = new \PDO($this->dsn);
            } else {
                throw new \RuntimeException('You need to enable either the SQLite3 or PDO_SQLite extension for the profiler to run properly.');
            }

            $db->exec('PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY;');
            $db->exec('CREATE TABLE IF NOT EXISTS rollerworks_passdbl (passwd STRING, created_at INTEGER)');
            $db->exec('CREATE UNIQUE INDEX IF NOT EXISTS passwd_idx ON rollerworks_passdbl (passwd)');

            $this->db = $db;
        }

        return $this->db;
    }

    protected function exec($db, $query, array $args = [])
    {
        if ($db instanceof \SQLite3) {
            $stmt = $this->prepareStatement($db, $query);

            foreach ($args as $arg => $val) {
                $stmt->bindValue($arg, $val, \is_int($val) ? \SQLITE3_INTEGER : \SQLITE3_TEXT);
            }

            $res = $stmt->execute();

            if ($res === false) {
                throw new \RuntimeException(sprintf('Error executing SQLite query "%s".', $query));
            }
            $res->finalize();
        } else {
            parent::exec($db, $query, $args);
        }
    }

    protected function fetch($db, $query, array $args = [])
    {
        $return = [];

        if ($db instanceof \SQLite3) {
            $stmt = $this->prepareStatement($db, $query);

            foreach ($args as $arg => $val) {
                $stmt->bindValue($arg, $val, \is_int($val) ? \SQLITE3_INTEGER : \SQLITE3_TEXT);
            }
            $res = $stmt->execute();

            while ($row = $res->fetchArray(\SQLITE3_ASSOC)) {
                $return[] = $row;
            }
            $res->finalize();
            $stmt->close();
        } else {
            $return = parent::fetch($db, $query, $args);
        }

        return $return;
    }
}
