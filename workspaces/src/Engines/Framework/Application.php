<?php

namespace Waystone\Workspaces\Engines\Framework;

use Keruald\Database\Database;
use Waystone\Workspaces\Engines\Errors\ErrorHandling;

class Application {

    public static function init () : void {
        Environment::init();
        ErrorHandling::init();
    }

    public static function getContext(array $config) : Context {
        $context = new Context();

        $context->config = $config;
        $context->db = Database::load($config["sql"]);
        $context->session = Session::load($context->db);
        $context->url = get_current_url_fragments();
        $context->initializeTemplateEngine($context->config['Theme']);

        return $context;
    }

}
