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
 * Interface for all update operation implementations.
 */
interface UpdateOperationInterface
{

    /**
     * Updates a row in a table by using its id.
     *
     * @param string $table
     *            The name of the table.
     * @param int $id
     *            Id of the row.
     * @param mixed[] $fields
     *            Map with field-value pairs to set into row.
     * @param string $fieldName
     *            Optional field name.
     * @return int Number of affected rows.
     */
    public function update(string $table, int $id, array $fields, string $fieldName = null): int;

    /**
     * Updates mupltiples rows in a table using a list of ids.
     *
     * @param string $table
     *            The name of the table.
     * @param int[] $ids
     *            List with ids of rows.
     * @param mixed[] $fields
     *            Map with field-value pairs to set into row.
     * @param string $fieldName
     *            Optional field name.
     * @return int Number of affected rows.
     */
    public function updateAll(string $table, array $ids, array $fields, string $fieldName = null): int;
}
