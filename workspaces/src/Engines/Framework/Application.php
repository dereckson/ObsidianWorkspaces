<?php

namespace Waystone\Workspaces\Engines\Framework;

use Keruald\Database\Database;
use Waystone\Workspaces\Engines\Errors\ErrorHandling;
use Waystone\Workspaces\Engines\Users\UserRepository;

class Application {

    public static function init () : void {
        Environment::init();
        ErrorHandling::init();
    }

    public static function getContext(array $config) : Context {
        $context = new Context();

        $context->config = $config;
        $context->db = Database::load($config["sql"]);
        $context->userRepository = new UserRepository($context->db);
        $context->resources = new Resources(
            $context->userRepository,
        );
        $context->session = Session::load(
            $context->db,
            $context->userRepository,
        );
        $context->url = get_current_url_fragments();
        $context->initializeTemplateEngine($context->config['Theme']);

        return $context;
    }

}
