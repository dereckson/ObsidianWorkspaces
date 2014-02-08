<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * CRUD features tests for each Collection class.
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Tests
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

require_once('../includes/autoload.php');

/**
 * A CollectionDocument class, for our tests.
 */
class BookDocument extends CollectionDocument {
    /**
     * @var string The book title
     */
    public $title;

    /**
     * @var string The book author
     */
    public $author;

    /**
     * Initializes a new instance of the BookDocument object
     */
    public function __construct ($author = null, $title = null) {
        if ($title !== null) $this->title = $title;
        if ($author !== null) $this->author = $author;
   }
}

/**
 * The tests for our basic, non storage engine specific CRUD features
 *
 * For coverage purposes, it requires a coversDefaultClass annotation in the classes using this trait.
 */
trait CRUDTestTrait {
    /**
     * @var BookDocument The document to test every CRUD feature
     */
    protected $redBook;

    /**
     * @var BookDocument A second document to test Set in two scenarii
     */
    protected $blueBook;


    /**
     * Initializes two documents for the tests
     */
    public function initializeDocuments() {
        $this->blueBook = new BookDocument('Isaac Asimov', 'Foundation');
        $this->redBook  = new BookDocument('Iain Banks', 'Matter'); //M. will be added in update test
        $this->redBook->id = 'redBook';
    }

    /**
     * @covers ::add
     * @covers ::exists
     * @covers ::update
     * @covers ::get
     * @covers ::set
     * @covers ::delete
     * @covers ::count
     * @covers ::getAll
     */
    public function testCRUD () {
        global $Config;
        $Config = static::getConfig();

        //::count
        $this->assertEquals(
            0, $this->collection->count(),
            "The test collection isn't empty. Check the last test run cleaned correctly the resources."
        );

        //::add
        $this->collection->add($this->redBook);
        $this->assertNotEmpty(
            $this->redBook->id,
            "After a document has been added, the is has been deleted."
        );
        $this->assertEquals(
            'redBook',
            $this->redBook->id,
            "After a document has been added, the is has been modified."
        );

        //:ccount
        $this->assertEquals(1, $this->collection->count());

        //::exists
        $this->assertFalse(
            $this->collection->exists($this->blueBook),
            "A non added document has been marked existing."
        );
        $this->assertTrue(
            $this->collection->exists($this->redBook),
            "An added document hasn't been found as existing."
        );

        //::update
        $this->redBook->author = 'Iain M. Banks';
        $this->collection->update($this->redBook);
        $this->assertEquals(
            'redBook',
            $this->redBook->id,
            "The document ID has been modified during an update operation. It should stay the same. Old id: redBook. New id: " . $this->redBook->id
        );

        //::count
        $this->assertEquals(1, $this->collection->count()
        );

        //::get - when our collection uses the generic CollectionDocument class
        $newDocument = $this->collection->get($this->redBook->id);
        $this->assertInstanceOf('CollectionDocument', $newDocument);
        $this->assertNotInstanceOf('BookDocument', $newDocument);

        //::set - when our collection uses a proper CollectionDocument descendant
        $this->collection->documentType = 'BookDocument';
        $newBook = $this->collection->get($this->redBook->id);
        $this->assertInstanceOf('BookDocument', $newBook);
        $this->assertObjectHasAttribute('title', $newBook);
        $this->assertObjectHasAttribute('author', $newBook);
        $this->assertObjectHasAttribute('id', $newBook);
        $this->assertEquals('Matter', $newBook->title);
        $this->assertEquals('Iain M. Banks', $newBook->author);
        $this->assertEquals('redBook', $newBook->id);

        //::set - an existing document as parameter
        $previousId = $this->redBook->id;
        $this->collection->set($this->redBook);
        $this->assertEquals(
            $previousId,
            $this->redBook->id,
            "The document ID has been modified during a set operation on an already added dcoument. It should stay the same. Old id: $previousId. New id: " . $this->redBook->id
        );

        //::count
        $this->assertEquals(1, $this->collection->count());

        //::set - a new document as parameter
        $this->collection->set($this->blueBook);
        $this->assertNotEmpty(
            $this->blueBook->id,
            "After a document has been added, the expected behavior is the id property is filled with the generated identifiant."
        );
        $this->assertTrue(
            $this->collection->exists($this->blueBook),
            "An added document with set hasn't been found as existing."
        );

        //::count
        $this->assertEquals(2, $this->collection->count());

        //::getAll
        $documents = $this->collection->getAll();
        $count = 0;
        foreach ($documents as $document) {
            switch ($document->id) {
                case $this->blueBook->id:
                    $this->assertInstanceOf('BookDocument', $document);
                    $this->assertObjectHasAttribute('title', $document);
                    $this->assertObjectHasAttribute('author', $document);
                    $this->assertObjectHasAttribute('id', $document);
                    $this->assertEquals('Foundation', $document->title);
                    $this->assertEquals('Isaac Asimov', $document->author);
                    break;

                case 'redBook':
                    $this->assertInstanceOf('BookDocument', $document);
                    $this->assertObjectHasAttribute('title', $document);
                    $this->assertObjectHasAttribute('author', $document);
                    $this->assertObjectHasAttribute('id', $document);
                    $this->assertEquals('Matter', $document->title);
                    $this->assertEquals('Iain M. Banks', $document->author);
                    break;

                default:
                    $this->fail('A document with an id nor redBook, the blueBook generated id has been returned.');
            }
            $count++;
        }
        $this->assertEquals(2, $count);

        //::delete
        $this->collection->delete($this->blueBook->id);
        $this->assertFalse(
            $this->collection->exists($this->blueBook),
            "A deleted document has been marked existing."
        );
        $this->assertTrue(
            $this->collection->exists($this->redBook),
            "An unexpected document has been deleted."
        );

        //::count
        $this->assertEquals(1, $this->collection->count());

        //::delete, ::count
        $this->collection->delete($this->redBook->id);
        $this->assertEquals(0, $this->collection->count());

        //::getAll
        $documents = $this->collection->getAll();
        $this->assertCount(0, $documents, "We expected each collection document would have been deleted.");
    }
}
