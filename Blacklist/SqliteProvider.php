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
class SqliteProvider implements BlacklistProviderInterface
{
    /**
     * @var \PDO
     */
    private $db;

    private $file;
    private $tableName;
    private $fieldName;

    /**
     * @param        $file
     * @param string $tableName
     * @param string $fieldName
     */
    public function __construct($file, $tableName = 'blacklist_passwords', $fieldName = 'word')
    {
        $this->file      = $file;
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
    }

    /**
     * {@inheritDoc}
     */
    public function isBlacklisted($password)
    {
        if (!$this->db) {
            $this->db = new \PDO("sqlite:" . $file);
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM " . $this->tableName ." WHERE " . $this->fieldName ." = :word");
        $stmt->bindValue(":word", $password, \PDO::PARAM_STR);
        $stmt->execute();

        list($count) = $stmt->fetch(\PDO::FETCH_NUM);
        $stmt->closeCursor();

        if ($count > 0) {
            return true;
        }

        return false;
    }
}
