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
    private $writeableAdapter = null;

    /**
     * Constructor.
     *
     * @param WriteableAdapterInterface $writeableAdapter
     *            The used adapter.
     */
    public function __construct(WriteableAdapterInterface $writeableAdapter)
    {
        $this->writeableAdapter = $writeableAdapter;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Nia\Sql\Operation\UpdateOperationInterface::update($table, $value, $fields, $fieldName)
     */
    public function update(string $table, $value, array $fields, string $fieldName = null): int
    {
        return $this->updateAll($table, [
            $value
        ], $fields, $fieldName);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Nia\Sql\Operation\UpdateOperationInterface::updateAll($table, $values, $fields, $fieldName)
     */
    public function updateAll(string $table, array $values, array $fields, string $fieldName = null): int
    {
        $fieldName = $fieldName ?? 'id';

        $columns = array_keys($fields);
        $columnValues = array_values($fields);

        // create SET syntax fieldlist with placeholders.
        $set = implode(', ', array_map(function (string $column) {
            return '`' . $column . '` = ?';
        }, $columns));

        // placeholders for values.
        $valueSet = implode(', ', array_fill(0, count($values), '?'));

        $sqlStatement = <<<EOL
            UPDATE
                `{$table}`
            SET
                {$set}
            WHERE
                `{$fieldName}` IN ({$valueSet});
EOL;

        $statement = $this->writeableAdapter->prepare($sqlStatement);
        foreach (array_merge($columnValues, $values) as $index => $value) {
            $statement->bindIndex($index + 1, $value, $this->determineType($value));
        }

        $statement->execute();

        return $statement->getNumRowsAffected();
    }
}
