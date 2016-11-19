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
namespace Test\Nia\Sql\Operation;

use PHPUnit_Framework_TestCase;
use Nia\Sql\Operation\FetchOperation;
use Nia\Sql\Adapter\PdoReadableAdapter;
use Nia\Sql\Adapter\PdoReadableAdapterInterface;
use PDO;

/**
 * Unit test for \Nia\Sql\Operation\FetchOperation.
 */
class FetchOperationTest extends PHPUnit_Framework_TestCase
{

    /** @var PdoReadableAdapterInterface */
    private $adapter = null;

    /** @var string */
    private $databaseFile = null;

    /**
     *
     * {@inheritdoc}
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->databaseFile = tempnam('/tmp', 'unittest-') . '.sqlite3';
        $sql = <<<SQL
DROP TABLE IF EXISTS `test`;
CREATE TABLE IF NOT EXISTS `test`
(
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `string` TEXT NOT NULL DEFAULT '',
    `int` INTEGER NOT NULL DEFAULT '0',
    `decimal` REAL NOT NULL DEFAULT '0',
    `bool` INTEGER NOT NULL DEFAULT '0',
    `nulled` INTEGER NULL
);
INSERT INTO test(`id`, `int`) VALUES(NULL, 4);
INSERT INTO test(`id`, `int`) VALUES(NULL, 5);
INSERT INTO test(`id`, `int`) VALUES(NULL, 4);
SQL;
        
        $pdo = new PDO('sqlite:' . $this->databaseFile, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $pdo->exec($sql);
        
        $this->adapter = new PdoReadableAdapter($pdo);
    }

    /**
     * @covers \Nia\Sql\Operation\FetchOperation::fetch
     */
    public function testFetch()
    {
        $operation = new FetchOperation($this->adapter);
        $result = $operation->fetch('test', 2);
        
        $this->assertSame([
            'id' => '2',
            'string' => '',
            'int' => '5',
            'decimal' => '0.0',
            'bool' => '0',
            'nulled' => null
        ], $result);
        
        $result = $operation->fetch('test', 5, 'int');
        
        $this->assertSame([
            'id' => '2',
            'string' => '',
            'int' => '5',
            'decimal' => '0.0',
            'bool' => '0',
            'nulled' => null
        ], $result);
        
        $this->setExpectedException(\OutOfBoundsException::class, 'Row "123" not found in table "test" using field "int".');
        
        $operation->fetch('test', 123, 'int');
    }

    /**
     * @covers \Nia\Sql\Operation\FetchOperation::fetchAll
     */
    public function testFetchAll()
    {
        $operation = new FetchOperation($this->adapter);
        $result = $operation->fetchAll('test', [
            1,
            3
        ]);
        
        $this->assertSame([
            [
                'id' => '1',
                'string' => '',
                'int' => '4',
                'decimal' => '0.0',
                'bool' => '0',
                'nulled' => null
            ],
            [
                'id' => '3',
                'string' => '',
                'int' => '4',
                'decimal' => '0.0',
                'bool' => '0',
                'nulled' => null
            ]
        ], $result);
        
        $result = $operation->fetchAll('test', [
            4
        ], 'int');
        
        $this->assertSame([
            [
                'id' => '1',
                'string' => '',
                'int' => '4',
                'decimal' => '0.0',
                'bool' => '0',
                'nulled' => null
            ],
            [
                'id' => '3',
                'string' => '',
                'int' => '4',
                'decimal' => '0.0',
                'bool' => '0',
                'nulled' => null
            ]
        ], $result);
        
        $result = $operation->fetchAll('test', [
            123
        ]);
        
        $this->assertSame([], $result);
    }
}
