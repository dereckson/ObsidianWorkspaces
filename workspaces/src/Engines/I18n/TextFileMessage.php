<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Text file Message class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  I18n
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

namespace Waystone\Workspaces\Engines\I18n;

use Keruald\OmniTools\IO\Directory;

use Exception;

/**
 * Represents a localizable message stored in a plain text file
 */
class TextFileMessage extends Message {

    /**
     * The folder where the message is stored.
     */
    public string $folder;

    /**
     * The message filename, without extension or language suffix.
     */
    public string $filename;

    /**
     * Initializes a new instance of the TextFileMessage class.
     *
     * @param string $folder The folder where the message is stored.
     * @param string $filename The message filename, without extension or language suffix.
     */
    public function __construct (string $folder, string $filename) {
        $this->folder = $folder;
        $this->filename = $filename;

        //Finds relevant files
        $dir = new Directory($folder);

        $files = $dir->glob($filename . '-*.txt');
        foreach ($files as $file) {
            $tokens = explode('-', $file->getFileNameWithoutExtension());
            if (count($tokens) > 2) {
                //The user have quux-lang.txt and quux-foo-lang.txt files
                continue;
            }
            $lang = $tokens[1];

            $this->localizations[$lang] = $file->read();
        }

        //Fallback if only one file is offered
        $file = $dir->getFile($filename . '.txt');
        if ($file->exists()) {
            if (count($this->localizations)) {
                if (array_key_exists(Language::FALLBACK, $this->localizations)) {
                    trigger_error("Ignored file: $filename.txt, as $filename-" . Language::FALLBACK . ".txt already exists and is used for fallback purpose", E_USER_NOTICE);
                    return;
                }
                trigger_error("You have $filename.txt and $filename-<lang>.txt files; you should have one or the other, but not both", E_USER_NOTICE);
            }

            $this->localizations[Language::FALLBACK] = $file->read();

            return;
        }

        if (!count($this->localizations)) {
            throw new Exception("TextFileMessage not found: $filename");
        }
    }
}
