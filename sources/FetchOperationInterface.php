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

use OutOfBoundsException;

/**
 * Interface for all fetch operation implementations.
 */
interface FetchOperationInterface
{

    /**
     * Fetches a row from a table by using a value.
     *
     * @param string $table
     *            The name of the table.
     * @param mixed $value
     *            The value.
     * @param string $fieldName
     *            Optional field name. If no field name is set, "id" will be used.
     * @throws OutOfBoundsException If no row could be found.
     * @return mixed[] The fetched row as a map.
     */
    public function fetch(string $table, $value, string $fieldName = null): array;

    /**
     * Fetches mupltiples rows from a table using a list of values.
     *
     * @param string $table
     *            The name of the table.
     * @param mixed[] $values
     *            List of values.
     * @param string $fieldName
     *            Optional field name. If no field name is set, "id" will be used.
     * @return mixed[] List containing maps with fetched rows.
     */
    public function fetchAll(string $table, array $values, string $fieldName = null): array;
}
