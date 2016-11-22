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
    private $readableAdapter = null;

    /**
     * Constructor.
     *
     * @param ReadableAdapterInterface $readableAdapter
     *            The used adapter.
     */
    public function __construct(ReadableAdapterInterface $readableAdapter)
    {
        $this->readableAdapter = $readableAdapter;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Nia\Sql\Operation\FetchOperationInterface::fetch($table, $value, $fieldName)
     */
    public function fetch(string $table, $value, string $fieldName = null): array
    {
        $fieldName = $fieldName ?? 'id';

        $rows = $this->fetchAll($table, [
            $value
        ], $fieldName);

        if (count($rows) !== 1) {
            throw new OutOfBoundsException(sprintf('Row "%s" not found in table "%s" using field "%s".', $value, $table, $fieldName));
        }

        return array_shift($rows);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Nia\Sql\Operation\FetchOperationInterface::fetchAll($table, $values, $fieldName)
     */
    public function fetchAll(string $table, array $values, string $fieldName = null): array
    {
        $fieldName = $fieldName ?? 'id';

        // placeholders for values.
        $valueSet = array_fill(0, count($values), '?');
        $valueSet = implode(', ', $valueSet);

        $sqlStatement = <<<EOL
            SELECT
                *
            FROM
                `{$table}`
            WHERE
                `{$fieldName}` IN ({$valueSet})
            ORDER BY
                `{$fieldName}`;
EOL;

        $statement = $this->readableAdapter->prepare($sqlStatement);
        foreach ($values as $index => $value) {
            $statement->bindIndex($index + 1, $value, $this->determineType($value));
        }

        $statement->execute();

        return $statement->fetchAll();
    }
}
