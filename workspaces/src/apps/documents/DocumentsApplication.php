<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Documents application class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  HelloWorld
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

use Waystone\Workspaces\Engines\Apps\Application;
use Waystone\Workspaces\Engines\Errors\ErrorHandling;
use Waystone\Workspaces\Engines\I18n\Language;

use Keruald\OmniTools\Collections\Vector;
use Keruald\OmniTools\IO\Directory;
use Keruald\OmniTools\IO\File;

/**
 * Documents application class
 */
class DocumentsApplication extends Application {

    /**
     * @var string the application name
     */
    public static $name = "Documents";

    private Directory $directory;

    protected function onAfterInitialize () : void {
        global $Config;

        $path = [
            $Config["Content"]["Workspaces"],
            $this->context->workspace->code,
            $this->context->configuration->path,
        ];

        $this->directory = Directory::fromPathFragments($path);
    }

    /**
     * Gets documents list
     *
     * @return Vector<bool> The documents list
     */
    public function getDocumentsList () : Vector {
        return $this->directory
            ->globFiles("*.json")
            ->map(fn (File $file) => $file->getFileNameWithoutExtension());
    }

    /**
     * Gets document
     *
     * @param string $docId the document identifier
     * @return stdClass the document JSON representation
     */
    public function getDocument ($docId) {
        $data = $this->directory->getFile($docId . ".json")->read();

        $data = json_decode($data);
        if ($data === null) {
            ErrorHandling::messageAndDie(
                GENERAL_ERROR,
                json_last_error_msg(),
                "Can't parse JSON to load $docId"
            );
        }

        if (property_exists($data, "type")) {
            $data->type = DocumentType::from(ucfirst($data->type));
        }

        $mapper = new JsonMapper();
        return $mapper->map($data, new Document);
    }

    public static function getDocumentType (DocumentType $type) : string {
        $key = 'DocumentType' . $type->value;
        return Language::get($key);
    }

    /**
     * Handles controller request
     */
    public function handleRequest () {
        //Reference to URL fragments and Smarty engine
        $url = $this->context->url;
        $smarty = $this->context->templateEngine;

        //Gets resources for HTML output
        if (count($url) == 1) {
            //Prints the list of the documents
            $documents = $this->getDocumentsList()->toArray();
            $smarty->assign('documents', $documents);
            $smarty->assign(
                "docs_url",
                $this->context->request->buildUrl($this->context->workspace->code, "docs")
            );
            $template = 'documents_list.tpl';
        } else {
            //Prints a document
            $docId = $url[1];
            $document = $this->getDocument($docId);
            $smarty->assign('documentId', $docId);
            $smarty->assign('documentType', self::getDocumentType($document->type));
            $smarty->assign('document', $document);
            $template = 'documents_view.tpl';
        }

        //Serves header
        $smarty->assign('PAGE_TITLE', "Documents");
        HeaderController::run($this->context);

        //Serves body
        $smarty->display('apps/documents/' . $template);

        //Serves footer
        FooterController::run($this->context);
    }
}
