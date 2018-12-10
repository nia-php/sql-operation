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

use PHPUnit\Framework\TestCase;
use Nia\Sql\Operation\InsertOperation;
use Nia\Sql\Adapter\PdoWriteableAdapterInterface;
use Nia\Sql\Adapter\PdoWriteableAdapter;
use PDO;

/**
 * Unit test for \Nia\Sql\Operation\InsertOperation.
 */
class InsertOperationTest extends TestCase
{

    /** @var PdoWriteableAdapterInterface */
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
        
        $this->adapter = new PdoWriteableAdapter($pdo);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        $this->adapter = null;
        if (is_file($this->databaseFile)) {
            unlink($this->databaseFile);
        }
    }

    /**
     * @covers \Nia\Sql\Operation\InsertOperation::insert
     */
    public function testInsert()
    {
        $operation = new InsertOperation($this->adapter);
        $result = $operation->insert('test', [
            'int' => 123
        ]);
        
        $this->assertSame(4, $result);
        
        $sql = 'SELECT `id`, `int` FROM `test` ORDER BY `id`;';
        
        $statement = $this->adapter->prepare($sql);
        $statement->execute();
        
        $this->assertSame([
            [
                'id' => '1',
                'int' => '4'
            ],
            [
                'id' => '2',
                'int' => '5'
            ],
            [
                'id' => '3',
                'int' => '4'
            ],
            [
                'id' => '4',
                'int' => '123'
            ]
        ], $statement->fetchAll());
    }
}
