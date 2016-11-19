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

use Nia\Sql\Adapter\Statement\StatementInterface;

/**
 * Trait providing a method to determine Nia\Sql\Adapter\Statement\StatementInterface::TYPE_* constant by using a value.
 */
trait DetermineTypeTrait
{

    /**
     * Determines a Nia\Sql\Adapter\Statement\StatementInterface::TYPE_* constant using the passed value.
     *
     * @param mixed $value
     *            The value.
     * @return int The determined Nia\Sql\Adapter\Statement\StatementInterface::TYPE_* constant.
     */
    private function determineType($value): int
    {
        // castings are much faster than is_{bool|string|null|float|...} functions.
        if ($value === (string) $value) {
            return StatementInterface::TYPE_STRING;
        } elseif ($value === (int) $value) {
            return StatementInterface::TYPE_INT;
        } elseif ($value === (bool) $value) {
            return StatementInterface::TYPE_BOOL;
        } elseif ($value === (float) $value) {
            return StatementInterface::TYPE_DECIMAL;
        } elseif ($value === null) {
            return StatementInterface::TYPE_NULL;
        }
        
        return StatementInterface::TYPE_STRING;
    }
}
