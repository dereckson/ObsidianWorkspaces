<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Unit testing — MySQLCollection class
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

use MySQLCollection;

/**
 * Tests MySQLCollection class
 */
#[CoversClass(MySQLCollection::class)]
class MySQLCollectionTest extends TestCase {
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
    /// Class initialisation
    ///

    /**
     * Gets default configuration for this test
     *
     * @return array The configuration block
     */
    protected static function getConfig () {
        return [
            'DocumentStorage' => [
                'Type' => 'MySQL',
                'Table' => UNITTESTING_MYSQL_TABLE
            ]
        ];
    }

    /**
     * Initializes the resources needed for thist test.
     */
    public function setUp () : void {
        $db = new MySQLDatabase(
            UNITTESTING_MYSQL_HOST,
            UNITTESTING_MYSQL_USERNAME,
            UNITTESTING_MYSQL_PASSWORD,
            UNITTESTING_MYSQL_DATABASE
        );

        $this->initializeDocuments();
        $this->collection = new MySQLCollection('quux', $db, UNITTESTING_MYSQL_TABLE);
    }

    ///
    /// Tests specific to MySQLCollection
    ///

    /**
     * Tests the property table is correctly set
     */
    public function testTable () {
        $this->assertEquals(UNITTESTING_MYSQL_TABLE, $this->collection->table, "The collection constructor should have initialized the MySQLCollection::table property.");
    }

    /**
     * Tests if the ready constant has been correctly defined
     */
    public function testReadyConstant () {
        $this->assertTrue(
            defined('COLLECTIONS_MYSQL_DATABASE_READY'),
            "After the client has been initialized, we shall have a 'COLLECTIONS_SQLITE_DATABASE_READY' constant defined."
        );

        $this->assertSame(
            COLLECTIONS_MYSQL_DATABASE_READY,
            true,
            "COLLECTIONS_MYSQL_DATABASE_READY constant value shall be the boolean true."
        );
    }

    /**
     * Tests if strings are correctly escaped
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
            "L\\'arbre",
            "\\'\\'",
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
}
