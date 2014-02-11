<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Unit testing — FilesCollection class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Tests
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

require '../includes/database/DatabaseException.php';

/**
 * Tests DatabaseException class
 */
class DatabaseExceptionTest extends PHPUnit_Framework_TestCase {
    /**
     * @covers DatabaseException::__construct
     * @covers DatabaseException::getQuery
     */
    public function testGetQuery () {
        $sql = 'SELECT 1+';
        $ex = new DatabaseException($sql, 'Syntax error', 1064);
        $this->assertEquals(
            $sql,
            $ex->getQuery(),
            ""
        );

        $ex = new DatabaseException();
        $this->assertNull(
            $ex->getQuery(),
            "If the query isn't specified during the constructor call, getQuery shall return null."
        );

        $ex = new DatabaseException('');
        $this->assertEquals(
            '',
            $ex->getQuery(),
            "If the query isn't specified during the constructor call, getQuery shall not return null but must return an empty string too."
        );
    }
}
