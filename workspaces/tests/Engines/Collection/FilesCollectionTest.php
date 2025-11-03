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

namespace Waystone\Workspaces\Tests\Engines\Collection;

use Waystone\Workspaces\Engines\Collection\FilesCollection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use Exception;

require_once(__DIR__ . '/../../../src//includes/GlobalFunctions.php');

/**
 * Tests FilesCollection class
 */
#[CoversClass(FilesCollection::class)]
class FilesCollectionTest extends TestCase {

    /**
     * @var string Our collection
     */
    protected $collection;

    /**
     * Gets default configuration for this test
     *
     * @return array The configuration block
     */
    protected static function getConfig () {
        return [
            'DocumentStorage' => [
                'Type' => 'Files',
                'Path' => UNITTESTING_FILESCOLLECTION_PATH
            ]
        ];
    }

    protected function setUp () : void {
        $this->initializeDocuments();

        global $Config;
        $Config = self::getConfig();
        $this->collection = new FilesCollection('quux');
    }

    ///
    /// Specific tests for this particular Collection class
    ///
    public function testConstructor () {
        global $Config;
        $Config = self::getConfig();

        $collection = new FilesCollection('quux');
        $this->assertEquals('quux', $collection->id);
    }

    public function testPaths () {
        global $Config;
        $Config = self::getConfig();

        $expectedPath = UNITTESTING_FILESCOLLECTION_PATH
                        . DIRECTORY_SEPARATOR
                        . 'quux';

        $this->assertEquals(
            $expectedPath,
            FilesCollection::getCollectionPath('quux')
        );

        $this->assertFileExists($expectedPath);

        $expectedPath .= DIRECTORY_SEPARATOR
                         .  'foo.json';
        $this->assertEquals(
            $expectedPath,
            $this->collection->getDocumentPath('foo')
        );
    }

    public function testConfigurationMissingException () {
        //Note: @expectedException isn't allowed in PHPUnit to test the generic Exception class.

        global $Config;
        $oldConfigDocumentStorage = $Config['DocumentStorage'];
        $Config['DocumentStorage'] = []; //Path isn't defined

        $this>$this->expectException(Exception::class);
        FilesCollection::getCollectionPath('quux');

        $Config['DocumentStorage'] = $oldConfigDocumentStorage;
    }

    public function testCantCreateDirectoryException () {
        global $Config;
        $oldConfigDocumentStorage = $Config['DocumentStorage'];
        $Config['DocumentStorage'] = [
            'Type' => 'Files',
            'Path' => '/root/obsidiancollections',
        ];

        $this->expectException(Exception::class);
        FilesCollection::getCollectionPath('quux');

        $Config['DocumentStorage'] = $oldConfigDocumentStorage;
    }

    ///
    /// CRUD tests
    ///

    use CRUDTestTrait;

    public function testFileContent () {
        global $Config;
        $Config = self::getConfig();

        $book = new BookDocument('Iain Banks', 'Excession');
        $book->id = 'greenBook';

        $this->collection->add($book);

        $filename = $this->collection->getDocumentPath('greenBook');

        $this->assertJsonFileEqualsJsonFile(
            'includes/collection/greenBook1.json',
            $filename
        );

        $book->author = 'Iain M. Banks';
        $this->collection->update($book);

        $this->assertJsonFileEqualsJsonFile(
            'includes/collection/greenBook2.json',
            $filename
        );

        //Cleans up, so CRUD test starts with an empty collection
        unlink(UNITTESTING_FILESCOLLECTION_PATH . '/quux/greenBook.json');
    }

    ///
    /// Cleanup
    ///

    /**
     * Tears down resources when tests are done
     */
    public static function tearDownAfterClass () : void {
        //Removes created directories
        rmdir(UNITTESTING_FILESCOLLECTION_PATH . '/quux');
        rmdir(UNITTESTING_FILESCOLLECTION_PATH);
    }

}
