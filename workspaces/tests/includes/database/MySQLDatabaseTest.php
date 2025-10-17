<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Unit testing — MysqlDatabaseTest class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Tests
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

//require '../src/includes/database/MysqlDatabase.php';

/**
 * Tests DatabaseTest
 */
class MySQLDatabaseTest extends PHPUnit_Framework_TestCase {
    /**
     * @var MysqlDatabase
     */
    private $db;

    /**
     * Creates the objects against which we will test.
     */
    public function setUp () {
        $this->db = new MySQLDatabase(
            UNITTESTING_MYSQL_HOST,
            UNITTESTING_MYSQL_USERNAME,
            UNITTESTING_MYSQL_PASSWORD,
            UNITTESTING_MYSQL_DATABASE
        );
    }

    /**
     * Tests string escape
     *
     * @covers SQLiteCollection::escape
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
                $this->db->escape($toEscapeExpressions[$i]),
                "The following string isn't properly escaped: '$toEscapeExpressions[$i]'"
            );
        }
    }
}
