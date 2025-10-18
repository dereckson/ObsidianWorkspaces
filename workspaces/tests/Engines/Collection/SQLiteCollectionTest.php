<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Unit testing — SQLiteCollection class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Tests
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

namespace Waystone\Workspaces\Tests\Engines\Collection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use SQLiteCollection;

/**
 * Tests SQLiteCollection class
 */
#[CoversClass(SQLiteCollection::class)]
class SQLiteCollectionTest extends TestCase {
    ///
    /// Traits
    ///

    use CRUDTestTrait;
    use SQLTestTrait;

    ///
    /// Test properties
    ///

    /**
     * @var string Our collection
     */
    protected $collection;

    ///
    /// Class initizliation
    ///

    /**
     * Gets default configuration for this test
     *
     * @return array The configuration block
     */
    public static function getConfig () {
        return [
            'DocumentStorage' => [
                'Type' => 'SQLite',
                'File' => UNITTESTING_SQLITE_FILE
            ]
        ];
    }

    public function setUp () : void {
        $this->initializeDocuments();

        global $Config;
        $Config = static::getConfig();

        $this->collection = new SQLiteCollection('quux');
    }

    ///
    /// Tests specific to SQLiteCollection
    ///
    /**
     * Tests the client related methods
     */
    public function testClient () {
        global $Config;
        $client1 = SQLiteCollection::getClient();
        $client2 = SQLiteCollection::getClient();

        $this->assertInstanceOf('SQLite3', $client1);
        $this->assertInstanceOf('SQLite3', $client2);
        $this->assertSame($client1, $client2, "The collection classes are expected to use a singleton pattern for the client: you should return the same object initialized before instead to create another one.");

        $databaseList = $this->collection->query("PRAGMA database_list");
        $this->assertEquals(
            [
                'seq' => 0,
                'name' => 'main',
                'file' => realpath($Config['DocumentStorage']['File'])
            ],
            $databaseList,
            "The query PRAGMA database_list hasn't returned what we expected: one database opened by the client, the file returned by the database matching our configuration file."
        );

        $this->assertTrue(
            defined('COLLECTIONS_SQLITE_DATABASE_READY'),
            "After the client has been initialized, we shall have a 'COLLECTIONS_SQLITE_DATABASE_READY' constant defined."
        );

        $this->assertSame(
            COLLECTIONS_SQLITE_DATABASE_READY,
            true,
            "COLLECTIONS_SQLITE_DATABASE_READY constant value shall be the boolean true."
        );
    }

    /**
     * Tests string escapement
     */
    public function testEscape () {
        $toEscapeExpressions = [
            "",
            "Lorem ipsum dolor",
            "L'arbre",
            "''",
            "cœur"
        ];
        $escapedExpressions = [
            "",
            "Lorem ipsum dolor",
            "L''arbre",
            "''''",
            "cœur"
        ];

        for ($i = 0 ; $i < count($toEscapeExpressions) ; $i++) {
            $this->assertEquals(
                $escapedExpressions[$i],
                $this->collection->escape($toEscapeExpressions[$i]),
                "The following string isn't properly escaped: '$toEscapeExpressions[$i]'"
            );
        }
    }

    public function testStatements () {
        $sqlQueries = [
            "SELECT foo FROM bar",
            "PRAGMA foo"
        ];

        $sqlStatements = [
            "UPDATE bar SET foo = 'quux'",
            "DELETE FROM bar WHERE foo = 'quux'",
            "INSERT INTO bar (foo) VALUES ('quux')",
            "REPLACE INTO bar (foo) VALUES ('quux')",
            "INSERT INTO bar SELECT FROM baz"
        ];

        foreach ($sqlQueries as $sql) {
            $this->assertFalse(
                $this->collection->isStatement($sql),
                "The query $sql should be considered as a query, not as a statement, to use SQLite3::query() and not SQLite3::exec()"
            );
        }

        foreach ($sqlStatements as $sql) {
            $this->assertTrue(
                $this->collection->isStatement($sql),
                "The query $sql should be considered as a statement, not as a query, as long as SQLite3 is concerned, to use SQLite3::exec() and not SQLite3::query()"
            );
        }
    }

    /**
     * Clears resources created for this test
     */
    public static function tearDownAfterClass () : void {
        unlink(UNITTESTING_SQLITE_FILE);
    }
}
