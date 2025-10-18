<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * SQL schema tests for each Collection class.
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Tests
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

namespace Waystone\Workspaces\Tests\Engines\Collection;

require_once(__DIR__ . '/../../../src/includes/autoload.php');

/**
 * The tests for our SQL storage engines, to ensure the schema is created correctly.
 *
 * For coverage purposes, it requires a coversDefaultClass annotation in the classes using this trait.
 */
trait SQLTestTrait {
    /**
     * @covers ::query()
     */
    public function testQuery () {
        /*
        The query method is a complicated aspect of the code, as it returns a different
        result according the kind of query:

            (1) If the query doesn't return any result, null.
            (2) If the query return a row with one field, the scalar value.
            (3) Otherwise, an associative array, the fields as keys, the row as values.
        */
        $sqlNoQueryResult   = "";
        $sqlNullResult = "UPDATE {$this->collection->table} SET collection_id = 'lorem' WHERE collection_id = 'lorem'";
        $sqlScalarResult = "SELECT 1+1";
        $sqlArrayResult  = "SELECT 8+2 as sum, 8*2 as product, 8/2 as quotient, 8-2 as difference";

        try {
            $resultNull = $this->collection->query($sqlNoQueryResult);
        } catch (Exception $ex) {
            $this->fail("The query() specifications provides: 'If the query doesn't return any result, return null.'. This is also the expected behavior for empty queries. Instead we got an exception.");

        }
        $this->assertNull($resultNull, "The query() specifications provides: 'If the query doesn't return any result, return null.'. This is also the expected behavior for empty queries.");

        $resultNull = $this->collection->query($sqlNullResult);
        $this->assertNull($resultNull, "The query() specifications provides: 'If the query doesn't return any result, return null.'. This is expected for $sqlNullResult.");

        $resultScalar = $this->collection->query($sqlScalarResult);
        $this->assertEquals(2, $resultScalar, "The query() specifications provides: 'If the query return a row with one field, the scalar value.' This is expected for $sqlScalarResult.");

        $resultArray = $this->collection->query($sqlArrayResult);
        $expectedResultArray = [
            'sum' => 10,
            'product' => 16,
            'quotient' => 4,
            'difference' => 6,
        ];
        $this->assertEquals($expectedResultArray, $resultArray, "The query() specifications provides: '[If the query returns a non scalar result, return] an associative array, the fields as keys, the row as values.'. This is expected for $sqlArrayResult.");
    }
}
