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
 * Interface for all insert operation implementations.
 */
interface InsertOperationInterface
{

    /**
     * Inserts a new row into a table with the given fields.
     *
     * @param string $table
     *            Name of the table to fill up.
     * @param mixed[] $fields
     *            Map with field-value pairs to insert into the table.
     * @return int The insert id.
     */
    public function insert(string $table, array $fields): int;
}
