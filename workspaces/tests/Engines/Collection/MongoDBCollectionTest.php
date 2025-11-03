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

namespace Waystone\Workspaces\Tests\Engines\Collection;

use Waystone\Workspaces\Engines\Collection\MongoDBCollection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests MongoDBCollection class
 */
#[CoversClass(MongoDBCollection::class)]
class MongoDBCollectionTest extends TestCase {
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

    public function setUp () : void {
        global $Config;
        $Config = static::getConfig();

        $this->initializeDocuments();

        $this->collection = new MongoDBCollection('quux');
    }

    use CRUDTestTrait;
}
