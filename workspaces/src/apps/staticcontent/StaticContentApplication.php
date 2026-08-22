<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Static content application class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  StaticContent
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

use Waystone\Workspaces\Engines\Apps\Application;

use Keruald\OmniTools\IO\Directory;

/**
 * Static content application class
 *
 * Serves the static files of a directory
 */
class StaticContentApplication extends Application {
    /**
     * @var string the application name
     */
    public static $name = "StaticContent";

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


    private function getFilePath ($file) {
        if ($file === "" || $file === NULL) {
            $file = "index.html";
        }

       return $this->directory->getFile($file);
    }

    public function serveFile ($file) {
        $file = $this->getFilePath($file);

        $smarty = $this->context->templateEngine;

        if ($file->exists()) {
            $path = $file->getPath();

            $ext = strtolower($file->getExtension());
            switch ($ext) {
                case "html":
                case "htm":
                    $smarty->assign('PAGE_TITLE', $title);
                    HeaderController::run($this->context);
                    include($path);
                    FooterController::run($this->context);
                    break;

                case "jpg": case "png": case "gif": case "svg": case "ico":
                case "css": case "js":
                case "txt": case "pdf": case "docx":
                    $type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
                    $fp = fopen($path, 'rb');
                    header('Content-Type: ' . $type);
                    header('Content-Length: ' . filesize($path));
                    ob_clean();
                    flush();
                    fpassthru($fp);
                    exit;

                default:
                    echo "Can't serve $ext file";
            }
        } else {
            ErrorPageController::show($this->context, 404);
            exit;
        }
    }

    /**
     * Handles controller request
     */
    public function handleRequest () {
        //Serves file from a static directory
        $this->serveFile($this->context->url[1]);
    }
}
