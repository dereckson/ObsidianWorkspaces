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

require_once('CRUDTestTrait.php');
require('../includes/GlobalFunctions.php');

/**
 * Tests FilesCollection class
 * @coversDefaultClass FilesCollection
 */
class FilesCollectionTest extends PHPUnit_Framework_TestCase {
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

    /**
     * Initializes a new instance of the PHPUnit_Framework_TestCase class
     *
     * @param string $name The test case name
     */
    public function __construct (string $name = null) {
        parent::__construct($name);

        $this->initializeDocuments();

        global $Config;
        $Config = self::getConfig();
        $this->collection = new FilesCollection('quux');
    }


    ///
    /// Specific tests for this particular Collection class
    ///

    /**
     * @covers FilesCollection::__construct
     */
    public function testConstructor () {
        global $Config;
        $Config = self::getConfig();

        $collection = new FilesCollection('quux');
        $this->assertEquals('quux', $collection->id);
    }

    /**
     * @covers FilesCollection::getCollectionPath
     * @covers FilesCollection::getDocumentPath
     */
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

    /**
     * @covers FilesCollection::getCollectionPath
     */
    public function testConfigurationMissingException () {
        //Note: @expectedException isn't allowed in PHPUnit to test the generic Exception class.

        global $Config;
        $oldConfigDocumentStorage = $Config['DocumentStorage'];
        $Config['DocumentStorage'] = []; //Path isn't defined
        try {
            FilesCollection::getCollectionPath('quux');
        } catch (Exception $ex) {
            $Config['DocumentStorage'] = $oldConfigDocumentStorage;
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @covers FilesCollection::getCollectionPath
     */
    public function testCantCreateDirectoryException () {
        //Note: @expectedException isn't allowed in PHPUnit to test the generic Exception class.

        global $Config;
        $oldConfigDocumentStorage = $Config['DocumentStorage'];
        $Config['DocumentStorage'] = [
            'Type' => 'Files',
            'Path' => '/root/obsidiancollections',
        ];
        try {
            FilesCollection::getCollectionPath('quux');
        } catch (Exception $ex) {
            $Config['DocumentStorage'] = $oldConfigDocumentStorage;
            return;
        }
        $this->fail("An expected exception has not been raised. If you're logged as root, you can safely delete /root/obsidiancollections folder and ignore this test. By the way, are you sure to trust a tests sequence creating and deleting files to run them as root?");
    }

    ///
    /// CRUD tests
    ///

    use CRUDTestTrait;

    /**
     * @covers FilesCollection::add
     * @covers FilesCollection::update
     */
    public function testFileContent () {
        global $Config;
        $Config = self::getConfig();

        $book = new BookDocument('Iain Banks', 'Excession');
        $book->id = 'greenBook';

        $this->collection->add($book);

        $filename = $this->collection->getDocumentPath('greenBook');

        $this->assertJsonFileEqualsJsonFile(
            'collections/greenBook1.json',
            $filename
        );

        $book->author = 'Iain M. Banks';
        $this->collection->update($book);

        $this->assertJsonFileEqualsJsonFile(
            'collections/greenBook2.json',
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
    public static function tearDownAfterClass () {
        //Removes created directories
        rmdir(UNITTESTING_FILESCOLLECTION_PATH . '/quux');
        rmdir(UNITTESTING_FILESCOLLECTION_PATH);}

}
