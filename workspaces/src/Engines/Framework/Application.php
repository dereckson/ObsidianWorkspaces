<?php

namespace Waystone\Workspaces\Engines\Framework;

use Waystone\Workspaces\Engines\Errors\ErrorHandling;
use Waystone\Workspaces\Engines\Users\UserRepository;

use Keruald\Cache\CacheFactory;
use Keruald\Database\Database;

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
        $context->url = get_current_url_fragments();
        $context->initializeTemplateEngine($context->config['Theme']);

        $context->cache = CacheFactory::load($context->config["cache"]);

        return $context;
    }

}
