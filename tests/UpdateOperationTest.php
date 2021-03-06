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
use Nia\Sql\Operation\UpdateOperation;
use PDO;
use Nia\Sql\Adapter\PdoWriteableAdapter;
use Nia\Sql\Adapter\PdoWriteableAdapterInterface;

/**
 * Unit test for \Nia\Sql\Operation\UpdateOperation.
 */
class UpdateOperationTest extends TestCase
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
INSERT INTO test(`id`, `string`, `int`) VALUES(NULL, 'foo', 4);
INSERT INTO test(`id`, `string`, `int`) VALUES(NULL, 'bar', 5);
INSERT INTO test(`id`, `string`, `int`) VALUES(NULL, 'baz', 4);
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
     * @covers \Nia\Sql\Operation\UpdateOperation::update
     */
    public function testUpdate()
    {
        $operation = new UpdateOperation($this->adapter);
        $result = $operation->update('test', 2, [
            'int' => 123
        ]);

        $this->assertSame(1, $result);

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
                'int' => '123'
            ],
            [
                'id' => '3',
                'int' => '4'
            ]
        ], $statement->fetchAll());

        $result = $operation->update('test', 'bar', [
            'int' => 456
        ], 'string');

        $this->assertSame(1, $result);

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
                'int' => '456'
            ],
            [
                'id' => '3',
                'int' => '4'
            ]
        ], $statement->fetchAll());
    }

    /**
     * @covers \Nia\Sql\Operation\UpdateOperation::updateAll
     */
    public function testUpdateAll()
    {
        $operation = new UpdateOperation($this->adapter);
        $result = $operation->updateAll('test', [
            4
        ], [
            'int' => 6
        ], 'int');

        $this->assertSame(2, $result);

        $sql = 'SELECT `id`, `int` FROM `test` ORDER BY `id`;';

        $statement = $this->adapter->prepare($sql);
        $statement->execute();

        $this->assertSame([
            [
                'id' => '1',
                'int' => '6'
            ],
            [
                'id' => '2',
                'int' => '5'
            ],
            [
                'id' => '3',
                'int' => '6'
            ]
        ], $statement->fetchAll());

        $result = $operation->updateAll('test', [
            'foo'
        ], [
            'int' => 7
        ], 'string');

        $this->assertSame(1, $result);

        $sql = 'SELECT `id`, `int` FROM `test` ORDER BY `id`;';

        $statement = $this->adapter->prepare($sql);
        $statement->execute();

        $this->assertSame([
            [
                'id' => '1',
                'int' => '7'
            ],
            [
                'id' => '2',
                'int' => '5'
            ],
            [
                'id' => '3',
                'int' => '6'
            ]
        ], $statement->fetchAll());
    }
}
