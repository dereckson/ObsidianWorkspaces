<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Hello World application class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  HelloWorld
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Hello World application class
 *
 * This class provides a sample of how an application is structured.
 */
class HelloWorldApplication extends Application {
    /**
     * @var string the application name
     */
    public static $name = "HelloWorld";

    /**
     * Handle controller request
     */
    public function handleRequest () {
        //Reference to URL fragments and Smarty engine
        $url = $this->context->url;
        $smarty = $this->context->templateEngine;

        //Serves header
        $smarty->assign('PAGE_TITLE', $title);
        HeaderController::Run($this->context);

        //Output Hello world
        echo "<p>Hello world!</p>"; //TODO: call an hello world view

        //Servers footer
        FooterController::Run($this->context);
    }
}
