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
     * @see \Nia\Sql\Operation\DeleteOperationInterface::delete($table, $id, $fieldName)
     */
    public function delete(string $table, int $id, string $fieldName = null): int
    {
        return $this->deleteAll($table, [
            $id
        ], $fieldName);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Nia\Sql\Operation\DeleteOperationInterface::deleteAll($table, $ids, $fieldName)
     */
    public function deleteAll(string $table, array $ids, string $fieldName = null): int
    {
        $fieldName = $fieldName ?? 'id';
        
        // placeholders for ids.
        $idsSet = array_fill(0, count($ids), '?');
        $idsSet = implode(', ', $idsSet);
        
        $sqlStatement = <<<EOL
            DELETE FROM
                `{$table}`
            WHERE
                `{$fieldName}` IN ({$idsSet});
EOL;
        
        $statement = $this->adapter->prepare($sqlStatement);
        foreach ($ids as $index => $value) {
            $statement->bindIndex($index + 1, $value, $this->determineType($value));
        }
        
        $statement->execute();
        
        return $statement->getNumRowsAffected();
    }
}
