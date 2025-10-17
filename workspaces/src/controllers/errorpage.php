<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Controller for error pages
 *
 * @package     ObsidianWorkspaces
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

/**
 * Error pages controller
 */
class ErrorPageController extends Controller {
    /**
     * @var int The HTTP error code
     */
    protected $errorCode;

    /**
     * Shows an error page
     *
     * @param Context $context The application or site context
     * @param $errorCode The HTTP error code
     */
    public static function show($context, $errorCode) {
        static::load($context)
            ->setErrorCode($errorCode)
            ->handleRequest();
    }

    /**
     * Sets HTTP error code
     *
     * @param $errorCode The HTTP error code
     * @return ErrorPageController the current instance
     */
    public function setErrorCode ($errorCode) {
        $this->errorCode = $errorCode;
        return $this;
    }

    /**
     * Handles controller request
     */
    public function handleRequest () {
        $smarty = $this->context->templateEngine;
        $smarty->assign("URL_HOME", get_url());

        switch ($this->errorCode) {
            case 404:
                header("HTTP/1.0 404 Not Found");
                $smarty->display("errors/404.tpl");
                break;

            default:
                die("Unknown error page: " . $this->errorCode);
        }
    }
}
