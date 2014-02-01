<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Disclaimer class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Model
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * Disclaimer class
 */
class Disclaimer {
    public $id;
    public $title;
    public $text;


    public function __construct ($id) {
        $this->id = $id;
    }

    public static function get ($id) {
        global $Config;
        $instance = new Disclaimer($id);

        try {
            $message = new TextFileMessage(
                $Config['Content']['Disclaimers'],
                $id
            );

            $data = (string)$message;
            $pos = strpos($data, "\n");
            if ($pos !== false) {
                $instance->title = substr($data, 0, $pos);
                $instance->text = trim(substr($data, $pos));
            } else {
                $instance->title = ucfirst($id);
                $instance->text = $data;
            }
        } catch (Exception $ex) {
            $instance->title = ucfirst($id);
            $instance->text = lang_get('NoSuchDisclaimer');
        }

        return $instance;
    }
}