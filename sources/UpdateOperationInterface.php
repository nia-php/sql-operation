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
     * Updates a row in a table by using a value.
     *
     * @param string $table
     *            The name of the table.
     * @param mixed $value
     *            The value.
     * @param mixed[] $fields
     *            Map with field-value pairs to set into row.
     * @param string $fieldName
     *            Optional field name. If no field name is set, "id" will be used.
     * @return int Number of affected rows.
     */
    public function update(string $table, $value, array $fields, string $fieldName = null): int;

    /**
     * Updates mupltiples rows in a table using a list of values.
     *
     * @param string $table
     *            The name of the table.
     * @param mixed[] $values
     *            List of values.
     * @param mixed[] $fields
     *            Map with field-value pairs to set into row.
     * @param string $fieldName
     *            Optional field name. If no field name is set, "id" will be used.
     * @return int Number of affected rows.
     */
    public function updateAll(string $table, array $values, array $fields, string $fieldName = null): int;
}
