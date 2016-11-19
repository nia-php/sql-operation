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
     * Fetches a row from a table by using its id.
     *
     * @param string $table
     *            The name of the table.
     * @param int $id
     *            Id of the row.
     * @param string $fieldName
     *            Optional field name.
     * @throws OutOfBoundsException If no row could be found.
     * @return mixed[] The fetched row as a map.
     */
    public function fetch(string $table, int $id, string $fieldName = null): array;

    /**
     * Fetches mupltiples rows from a table using a list of ids.
     *
     * @param string $table
     *            The name of the table.
     * @param int[] $ids
     *            List with ids of rows.
     * @param string $fieldName
     *            Optional field name.
     * @return mixed[] List containing maps with fetched rows.
     */
    public function fetchAll(string $table, array $ids, string $fieldName = null): array;
}
