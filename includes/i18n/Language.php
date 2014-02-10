<?php

/**
 *    _, __,  _, _ __, _  _, _, _
 *   / \ |_) (_  | | \ | /_\ |\ |
 *   \ / |_) , ) | |_/ | | | | \|
 *    ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
 *
 * Localization (l10n) language class
 *
 * @package     ObsidianWorkspaces
 * @subpackage  I18n
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 */

/**
 * Gets a specified language expression defined in configuration file
 *
 * @param string $key the configuration key matching the value to get
 * @return string The value in the configuration file
 * @deprecated
 */
function lang_get ($key) {
    trigger_error("The use of the L10n global functions is deprecated. Call Language::get('$key') instead.", E_USER_DEPRECATED);
    return Language::get($key);
}

/**
 * Language services
 */
class Language implements LoadableWithContext {
    ///
    /// Properties
    ///

    /**
     * @var
     */
    const FALLBACK = 'en';

    /**
     * @var Smarty the template engine
     */
    private $templateEngine;

    ///
    /// Singleton pattern. Constructor.
    ///

    /**
     * @var Language The loaded Language instance
     */
    private static $instance;

    /**
     * Loads an instance of the class
     *
     * @param Context $context The context
     * @return Language An instance of the Language class
     */
    public static function Load (Context $context = null) {
        if (static::$instance === null) {
            //Initializes an instance
            if ($context === null) {
                throw new InvalidArgumentException("A context is required to load this class for the first time.");
            }

            if ($context->templateEngine === null) {
                throw new InvalidArgumentException("A context is required to load this class for the first time. You provided one, but the template engine isn't initiliazed. This is required, as the languages files are managed by the template engine.");
            }

            static::$instance = new static($context->templateEngine);
        }

        return static::$instance;
    }

    /**
     * Initializes a new instance of the Language class
     */
    public function __construct ($templateEngine) {
        $this->templateEngine = $templateEngine;
    }

    ///
    /// Static helper methods
    ///

    /**
     * Defines the LANG constant, to lang to print
     *
     * This information is contained in the session, or if not yet defined,
     * it's to determine according the user's browser preferences.
     * @see findLanguage
     */
    public static function initialize () {
        //If $_SESSION['lang'] doesn't exist yet, find a common language
        if (!array_key_exists('lang', $_SESSION)) {
            $lang = static::findLanguage();
            $_SESSION['lang'] = $lang ? $lang : '-';
        }

        if ($_SESSION['lang'] != '-') {
            define('LANG', $_SESSION['lang']);
        }
    }

    /**
     * Gets a common lang spoken by the site and the user's browser
     * @see Language::getHttpAcceptLanguages
     *
     * @return string the language
     */
    public static function findLanguage () {
        if (file_exists('lang') && is_dir('lang')) {
            //Gets lang/ subdirectories: this is the list of available languages
            $handle = opendir('lang');
            while ($file = readdir($handle)) {
                if ($file != '.' && $file != '..' && is_dir("lang/$file")) {
                    $langs[] = $file;
                }
            }

            //The array $langs contains now the language available.
            //Gets the langs the user should want:
            if (!$userlangs = static::getHttpAcceptLanguages())
                return;

            //Gets the intersection between the both languages arrays
            //If it matches, returns first result
            $intersect = array_intersect($userlangs, $langs);
            if (count($intersect)) {
                return array_shift($intersect);
            }

            //Now it's okay with Opera and Firefox but Internet Explorer will
            //by default return en-US and not en or fr-BE and not fr, so second pass
            foreach ($userlangs as $userlang) {
                $lang = explode('-', $userlang);
                if (count($lang) > 1)
                    $userlangs2[] = $lang[0];
            }
            $intersect = array_intersect($userlangs2, $langs);
            if (count($intersect)) {
                return array_shift($intersect);
            }
        }
    }

    /**
     * Gets the languages accepted by the browser, by order of priority.
     *
     * This will read the HTTP_ACCEPT_LANGUAGE variable sent by the browser in the
     * HTTP request.
     *
     * @return Array an array of string, each item a language accepted by browser
     */
    public static function getHttpAcceptLanguages () {
        //What language to print is sent by browser in HTTP_ACCEPT_LANGUAGE var.
        //This will be something like en,fr;q=0.8,fr-fr;q=0.5,en-us;q=0.3

        if (!isset($_SERVER) || !array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            return null;
        }

        $http_accept_language = explode(',', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        foreach ($http_accept_language as $language) {
            $userlang = explode(';q=', $language);
            if (count($userlang) == 1) {
                $userlangs[] = array(1, $language);
            } else {
                $userlangs[] = array($userlang[1], $userlang[0]);
            }
        }
        rsort($userlangs);
        foreach ($userlangs as $userlang) {
            $result[] = $userlang[1];
        }
        return $result;
    }

    public static function get ($key) {
        return static::load()->getConfigVar($key);
    }

    ///
    /// Methods
    ///

    /**
     * Loads specified language Smarty configuration file
     *
     * @param Smarty $templateEngine the template engine
     * @param string $file the file to load
     * @param mixed $sections array of section names, single section or null
     */
    public function configLoad ($file, $sections = null) {
        $fallback = static::FALLBACK;

        //Loads English file as fallback if some parameters are missing
        if (file_exists("lang/$fallback/$file")) {
            $this->templateEngine->configLoad("lang/$fallback/$file", $sections);
        }

        //Loads wanted file (if it exists and a language have been defined)
        if (defined('LANG') && LANG != '$fallback' && file_exists('lang/' . LANG . '/' . $file)) {
            $this->templateEngine->configLoad('lang/' . LANG . '/' . $file, $sections);
        }
    }

    /**
     * Gets a specified language expression defined in configuration file
     *
     * @param string $key the configuration key matching the value to get
     * @return string The value in the configuration file
     */
    private function getConfigVar ($key) {
        $configValue = $this->templateEngine->config_vars[$key];
        return $configValue ? $configValue : "#$key#";
    }
}
