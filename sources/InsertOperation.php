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
 * Default insert operation implementation.
 */
class InsertOperation implements InsertOperationInterface
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
     * @see \Nia\Sql\Operation\InsertOperationInterface::insert($table, $fields)
     */
    public function insert(string $table, array $fields): int
    {
        $columns = array_map(function ($column) {
            return '`' . $column . '`';
        }, array_keys($fields));
        
        $fieldlist = implode(', ', $columns);
        $placeholders = str_repeat('?, ', count($fields) - 1) . '?';
        
        $sqlStatement = <<<EOL
            INSERT INTO
                `{$table}` ({$fieldlist})
            VALUES
                ({$placeholders});
EOL;
        
        $statement = $this->writeableAdapter->prepare($sqlStatement);
        foreach (array_values($fields) as $index => $value) {
            $statement->bindIndex($index + 1, $value, $this->determineType($value));
        }
        
        $statement->execute();
        
        return (int) $this->writeableAdapter->getLastInsertId();
    }
}
