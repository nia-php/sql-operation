<?php
/*
 * This file is part of the nia framework.
 *
 * (c) Patrick Ullmann <patrick.ullmann@nat-software.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types = 1);
namespace Nia\Sql\Operation;

use Nia\Sql\Adapter\WriteableAdapterInterface;

/**
 * Default update operation implementation.
 */
class UpdateOperation implements UpdateOperationInterface
{
    use DetermineTypeTrait;

    /**
     * The used adapter.
     *
     * @var WriteableAdapterInterface
     */
    private $adapter = null;

    /**
     * Constructor.
     *
     * @param WriteableAdapterInterface $adapter
     *            The used adapter.
     */
    public function __construct(WriteableAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Nia\Sql\Operation\UpdateOperationInterface::update($table, $id, $fields, $fieldName)
     */
    public function update(string $table, int $id, array $fields, string $fieldName = null): int
    {
        return $this->updateAll($table, [
            $id
        ], $fields, $fieldName);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Nia\Sql\Operation\UpdateOperationInterface::updateAll($table, $ids, $fields, $fieldName)
     */
    public function updateAll(string $table, array $ids, array $fields, string $fieldName = null): int
    {
        $fieldName = $fieldName ?? 'id';
        
        $columns = array_keys($fields);
        $values = array_values($fields);
        
        // create SET syntax fieldlist with placeholders.
        $set = implode(', ', array_map(function (string $column) {
            return '`' . $column . '` = ?';
        }, $columns));
        
        // placeholders for ids.
        $idsSet = implode(', ', array_fill(0, count($ids), '?'));
        
        $sqlStatement = <<<EOL
            UPDATE
                `{$table}`
            SET 
                {$set}
            WHERE
                `{$fieldName}` IN ({$idsSet});
EOL;
        
        $statement = $this->adapter->prepare($sqlStatement);
        foreach (array_merge($values, $ids) as $index => $value) {
            $statement->bindIndex($index + 1, $value, $this->determineType($value));
        }
        
        $statement->execute();
        
        return $statement->getNumRowsAffected();
    }
}
