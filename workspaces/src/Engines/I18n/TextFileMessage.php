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

use Exception;

/**
 * Represents a localizable message stored in a plain text file
 */
class TextFileMessage extends Message {

    /**
     * @var string The folder where the message is stored.
     */
    public $folder;

    /**
     * @var string The message filename, without extension or language suffix.
     */
    public $filename;

    /**
     * Initializes a new instance of the TextFileMessage class.
     *
     * @param string $folder The folder where the message is stored.
     * @param string $filename The message filename, without extension or language suffix.
     */
    public function __construct ($folder, $filename) {
        $this->folder = $folder;
        $this->folder = $filename;

        //Finds relevant files
        $files = scandir($folder);
        foreach ($files as $file) {
            if (string_starts_with($file, $filename . '-') && get_extension($file) == 'txt') {
                $lang = substr($file, strlen($filename) + 1, -4);
                if (strpos($lang, '-') !== false) {
                    //The user have quux-lang.txt and quux-foo-lang.txt files
                    continue;
                }
                $file = $folder . DIRECTORY_SEPARATOR . $file;
                $this->localizations[$lang] = file_get_contents($file);
            }
        }

        //Fallback if only one file is offered
        $file = $folder . DIRECTORY_SEPARATOR . $filename . '.txt';
        if (file_exists($file)) {
            if (count($this->localizations)) {
                if (array_key_exists(MESSAGE_FALLBACK_LANG, $this->localizations)) {
                    trigger_error("Ignored file: $filename.txt, as $filename-" . MESSAGE_FALLBACK_LANG . ".txt already exists and is used for fallback purpose", E_USER_NOTICE);
                    return;
                }
                trigger_error("You have $filename.txt and $filename-<lang>.txt files; you should have one or the other, but not both", E_USER_NOTICE);
            }


            $this->localizations[MESSAGE_FALLBACK_LANG] = file_get_contents($file);
            return;
        }

        if (!count($this->localizations)) {
            throw new Exception("TextFileMessage not found: $filename");
        }
    }
}
