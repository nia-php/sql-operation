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
 * Default delete operation implementation.
 */
class DeleteOperation implements DeleteOperationInterface
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
     * @see \Nia\Sql\Operation\DeleteOperationInterface::delete($table, $value, $fieldName)
     */
    public function delete(string $table, $value, string $fieldName = null): int
    {
        return $this->deleteAll($table, [
            $value
        ], $fieldName);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Nia\Sql\Operation\DeleteOperationInterface::deleteAll($table, $values, $fieldName)
     */
    public function deleteAll(string $table, array $values, string $fieldName = null): int
    {
        $fieldName = $fieldName ?? 'id';

        // placeholders for values.
        $valueSet = array_fill(0, count($values), '?');
        $valueSet = implode(', ', $valueSet);

        $sqlStatement = <<<EOL
            DELETE FROM
                `{$table}`
            WHERE
                `{$fieldName}` IN ({$valueSet});
EOL;

        $statement = $this->writeableAdapter->prepare($sqlStatement);
        foreach ($values as $index => $value) {
            $statement->bindIndex($index + 1, $value, $this->determineType($value));
        }

        $statement->execute();

        return $statement->getNumRowsAffected();
    }
}
