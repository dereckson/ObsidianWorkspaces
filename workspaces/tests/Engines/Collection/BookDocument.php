<?php

namespace Waystone\Workspaces\Tests\Engines\Collection;

use CollectionDocument;

require_once(__DIR__ . '/../../../src/includes/autoload.php');

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
