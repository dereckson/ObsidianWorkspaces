<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Permission class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Model
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * Permission class
 */
class Permission {
    /**
     * Gets resource type letter from code
     *
     * @param string $code The resource type code
     * @return string The resource type letter
     */
    public static function getResourceTypeLetterFromCode ($code) {
        switch ($code) {
            case "user": return 'U';
            case "group": return 'G';
            case "workspace": return 'W';

            default:
                throw new InvalidArgumentException("Not a resource type code: $code");
        }
    }

    /**
     * Gets resource type code from letter
     *
     * @param string $letter The resource type letter
     * @return string The resource type code
     */
    public static function getResourceTypeCodeFromLetter($letter) {
        switch ($letter) {
            case 'U': return "user";
            case 'G': return "group";
            case 'W': return "workspace";

            default:
                throw new InvalidArgumentException("Not a resource type letter: $letter");
        }
    }
}
