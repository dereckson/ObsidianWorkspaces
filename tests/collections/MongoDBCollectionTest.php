<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Unit testing — MongoDBCollection class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Tests
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

require_once('CRUDTestTrait.php');

/**
 * Tests MongoDBCollection class
 * @coversDefaultClass MongoDBCollection
 */
class MongoDBCollectionTest extends PHPUnit_Framework_TestCase {
    /**
     * @var string Our collection
     */
    protected $collection;

    /**
     * Gets default configuration for this test
     *
     * @return array The configuration block
     */
    public static function getConfig () {
        return [
            'DocumentStorage' => [
                'Type' => 'MongoDB',
                'Host' => UNITTESTING_MONGODB_HOST,
                'Port' => UNITTESTING_MONGODB_PORT,
                'SSL' => UNITTESTING_MONGODB_SSL ? [] : null
            ]
        ];
    }

    /**
     * Initializes a new instance of the PHPUnit_Framework_TestCase class
     *
     * @param string $name The test case name
     */
    public function __construct (string $name = null) {
        parent::__construct($name);

        global $Config;
        $Config = static::getConfig();

        $this->initializeDocuments();

        $this->collection = new MongoDBCollection('quux');
    }

    use CRUDTestTrait;
}
