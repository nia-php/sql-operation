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

use Nia\Sql\Adapter\ReadableAdapterInterface;
use OutOfBoundsException;

/**
 * Default fetch operation implementation.
 */
class FetchOperation implements FetchOperationInterface
{
    use DetermineTypeTrait;

    /**
     * The used adapter.
     *
     * @var ReadableAdapterInterface
     */
    private $adapter = null;

    /**
     * Constructor.
     *
     * @param ReadableAdapterInterface $adapter
     *            The used adapter.
     */
    public function __construct(ReadableAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Nia\Sql\Operation\FetchOperationInterface::fetch($table, $id, $fieldName)
     */
    public function fetch(string $table, int $id, string $fieldName = null): array
    {
        $fieldName = $fieldName ?? 'id';
        
        $rows = $this->fetchAll($table, [
            $id
        ], $fieldName);
        
        if (count($rows) !== 1) {
            throw new OutOfBoundsException(sprintf('Row "%d" not found in table "%s" using field "%s".', $id, $table, $fieldName));
        }
        
        return array_shift($rows);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Nia\Sql\Operation\FetchOperationInterface::fetchAll($table, $ids, $fieldName)
     */
    public function fetchAll(string $table, array $ids, string $fieldName = null): array
    {
        $fieldName = $fieldName ?? 'id';
        
        // placeholders for ids.
        $idsSet = array_fill(0, count($ids), '?');
        $idsSet = implode(', ', $idsSet);
        
        $sqlStatement = <<<EOL
            SELECT 
                * 
            FROM 
                `{$table}` 
            WHERE 
                `{$fieldName}` IN ({$idsSet}) 
            ORDER BY 
                `{$fieldName}`;
EOL;
        
        $statement = $this->adapter->prepare($sqlStatement);
        foreach ($ids as $index => $value) {
            $statement->bindIndex($index + 1, $value, $this->determineType($value));
        }
        
        $statement->execute();
        
        return $statement->fetchAll();
    }
}
