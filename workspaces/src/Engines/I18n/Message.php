<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Message class
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
 * Represents a localizable message
 */
class Message {

    /**
     * @var Array the localized message
     */
    public $localizations = [];

    /**
     * Initializes a new instance of the Message class
     *
     * @param mixed $expression unique string or localizations Array
     */
    public function __construct ($expression) {
        if (is_array($expression)) {
            if (count($expression) && is_array($expression[0])) {
                foreach ($expression as $msg) {
                    $this->localizations[$msg[0]] = $msg[1];
                }
            } else {
                $this->localizations = $expression;
            }
        } elseif (is_string($expression)) {
            $this->localizations = [
                Language::FALLBACK => $expression
            ];
        } else {
            throw new Exception("Expression must be a string or a l10n array");
        }
    }
    
    /**
     * Gets a string representation of the message
     *
     * @return string The message string representation
     */
    public function __toString () {
        if (!count($this->localizations)) {
            return "";
        }

        if (!defined('LANG') || !array_key_exists(LANG, $this->localizations)) {
            if (array_key_exists(Language::FALLBACK, $this->localizations)) {
                return $this->localizations[Language::FALLBACK];
            }
            return array_values($this->localizations)[0];
        }

        return $this->localizations[LANG];
    }
}
