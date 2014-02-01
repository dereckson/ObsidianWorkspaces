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

/**
 * Documents application class
 */
class DocumentsApplication extends Application {
    /**
     * @var string the application name
     */
    public static $name = "Documents";

    /**
     * Gets path to a document file
     */
    private function getFilePath ($file) {
        global $Config;

        return $Config['Content']['Workspaces']
            . DIRECTORY_SEPARATOR
            . $this->context->workspace->code
            . DIRECTORY_SEPARATOR
            . $this->context->configuration->path
            . DIRECTORY_SEPARATOR
            . $file;
    }

    /**
     * Gets documents list
     *
     * @return array The documents list
     */
    public function getDocumentsList () {
        $dir = $this->getFilePath('');
        $files = scandir($dir);
        $documents = [];
        foreach ($files as $file) {
            if (get_extension($file) == 'json') {
                $documents[] = get_filename($file);
            }
        }
        return $documents;
    }

    /**
     * Gets document
     *
     * @param string $docId the document identifier
     * @return stdClass the document JSON representation
     */
    public function getDocument ($docId) {
        $file = $this->getFilePath($docId . '.json');
        $data = file_get_contents($file);
        return json_decode($data);
    }

    public static function getDocumentType ($type) {
        $key = 'DocumentType' . ucfirst(strtolower($type));
        return lang_get($key);
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
            $documents = $this->getDocumentsList();
            $smarty->assign('documents', $documents);
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
        HeaderController::Run($this->context);

        //Serves body
        $smarty->display('apps/documents/' . $template);

        //Serves footer
        FooterController::Run($this->context);
    }
}
