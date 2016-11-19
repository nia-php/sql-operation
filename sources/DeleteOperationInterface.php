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

/**
 * Interface for all delete operation implementations.
 */
interface DeleteOperationInterface
{

    /**
     * Deletes a row from a table by using its id.
     *
     * @param string $table
     *            The name of the table.
     * @param int $id
     *            Id of the row.
     * @param string $fieldName
     *            Optional field name.
     * @return int Number of affected rows.
     */
    public function delete(string $table, int $id, string $fieldName = null): int;

    /**
     * Deletes mupltiples rows from a table using a list of ids.
     *
     * @param string $table
     *            The name of the table.
     * @param int[] $ids
     *            List with ids of rows.
     * @param string $fieldName
     *            Optional field name.
     * @return int Number of affected rows.
     */
    public function deleteAll(string $table, array $ids, string $fieldName = null): int;
}
