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
use Nia\Sql\Operation\DeleteOperation;
use Nia\Sql\Adapter\PdoWriteableAdapterInterface;
use Nia\Sql\Adapter\PdoWriteableAdapter;
use PDO;

/**
 * Unit test for \Nia\Sql\Operation\DeleteOperation.
 */
class DeleteOperationTest extends TestCase
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
     * @covers \Nia\Sql\Operation\DeleteOperation::delete
     */
    public function testDelete()
    {
        $operation = new DeleteOperation($this->adapter);
        $result = $operation->delete('test', 2);

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
                'id' => '3',
                'int' => '4'
            ]
        ], $statement->fetchAll());
        $result = $operation->delete('test', 'baz', 'string');

        $this->assertSame(1, $result);

        $sql = 'SELECT `id`, `int` FROM `test` ORDER BY `id`;';

        $statement = $this->adapter->prepare($sql);
        $statement->execute();

        $this->assertSame([
            [
                'id' => '1',
                'int' => '4'
            ]
        ], $statement->fetchAll());
    }

    /**
     * @covers \Nia\Sql\Operation\DeleteOperation::deleteAll
     */
    public function testDeleteAll()
    {
        $operation = new DeleteOperation($this->adapter);
        $result = $operation->deleteAll('test', [
            4
        ], 'int');

        $this->assertSame(2, $result);

        $sql = 'SELECT `id`, `int` FROM `test` ORDER BY `id`;';

        $statement = $this->adapter->prepare($sql);
        $statement->execute();

        $this->assertSame([
            [
                'id' => '2',
                'int' => '5'
            ]
        ], $statement->fetchAll());

        $result = $operation->deleteAll('test', [
            'bar'
        ], 'string');

        $this->assertSame(1, $result);

        $sql = 'SELECT `id`, `int` FROM `test` ORDER BY `id`;';

        $statement = $this->adapter->prepare($sql);
        $statement->execute();

        $this->assertSame([], $statement->fetchAll());
    }
}
