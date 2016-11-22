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
     * Deletes a row from a table by using a value.
     *
     * @param string $table
     *            The name of the table.
     * @param mixed $value
     *            The value.
     * @param string $fieldName
     *            Optional field name. If no field name is set, "id" will be used.
     * @return int Number of affected rows.
     */
    public function delete(string $table, $value, string $fieldName = null): int;

    /**
     * Deletes mupltiples rows from a table using a list of values.
     *
     * @param string $table
     *            The name of the table.
     * @param mixed[] $values
     *            List of values.
     * @param string $fieldName
     *            Optional field name. If no field name is set, "id" will be used.
     * @return int Number of affected rows.
     */
    public function deleteAll(string $table, array $values, string $fieldName = null): int;
}
