<?php

namespace Waystone\Workspaces\Engines\Framework;

use Waystone\Workspaces\Engines\Errors\ErrorHandling;
use Waystone\Workspaces\Engines\Users\UserRepository;

use Keruald\Cache\CacheFactory;
use Keruald\Database\Database;
use Keruald\OmniTools\HTTP\Requests\Request;

class Application {

    public static function init () : void {
        Environment::init();
        ErrorHandling::init();
    }

    public static function getContext(array $config) : Context {
        $context = new Context();

        $context->config = $config;
        $context->db = Database::load($config["sql"]);
        $context->resources = new Resources(
            new UserRepository($context->db),
        );
        $context->session = Session::load(
            $context->db,
            $context->resources->users,
        );

        $request = (new Request())
            ->withBaseUrl($config["BaseURL"]);
        $context->request = $request;
        $context->url = self::getCurrentUrlFragments($request);

        $context->initializeTemplateEngine($context->config['Theme']);

        $context->cache = CacheFactory::load($context->config["cache"]);

        return $context;
    }

   /**
    * Gets an array of URL fragments to be processed by controller
    *
    * @return array an array containing URL fragments
    */
    private static function getCurrentUrlFragments (Request $request) : array {
        $currentUrl = $request->getCurrentUrl();

        if ($currentUrl === "/index.php") {
            return [];
        }

        return explode("/", substr($currentUrl, 1));
    }

}
