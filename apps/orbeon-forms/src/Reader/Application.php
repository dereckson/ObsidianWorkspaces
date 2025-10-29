<?php

namespace Waystone\Apps\OrbeonForms\Reader;

use Waystone\Apps\OrbeonForms\Forms\Form;
use Waystone\Workspaces\Engines\Apps\Application as BaseApplication;
use Waystone\Workspaces\Engines\Errors\ErrorHandling;

use Keruald\Database\Engines\PDOEngine;
use Keruald\OmniTools\Collections\Vector;
use Keruald\OmniTools\DataTypes\Option\None;
use Keruald\OmniTools\DataTypes\Option\Option;
use Keruald\OmniTools\DataTypes\Option\Some;

use ErrorPageController;
use FooterController;
use HeaderController;

class Application extends BaseApplication {

    private PDOEngine $db;

    protected function onAfterInitialize () : void {
        /** @var ApplicationConfiguration $config */
        $config = $this->context->configuration;

        $this->db = PDOEngine::initialize($config->orbeonDatabase);
    }

    ///
    /// Controller methods
    ///

    /**
     * Serves the application index page
     * @param Vector<Form> $forms
     */
    private function index (Vector $forms) : void {
        $count = $forms->count();

        if ($count == 0) {
            ErrorHandling::messageAndDie(GENERAL_ERROR, "No form set in workspace configuration");
        }

        if ($count == 1) {
            $slug = $forms[0]["slug"];
            $this->redirectTo($slug);
        }

        ///
        /// View: List of forms
        ///

        $smarty = $this->context->templateEngine;

        // List of forms, with their URL
        $forms = $forms->map(function ($form) {
            return [
                "name" => $form["name"],
                "url" => $this->buildUrl($form["slug"]),
            ];
        });

        // Header
        $smarty->assign("PAGE_TITLE", "Forms");
        HeaderController::run($this->context);

        // Body
        $smarty->display("apps/_blocks/page_header.tpl");

        $smarty->assign("items", $forms);
        $smarty->display("apps/_blocks/menu_items.tpl");

        // Footer
        FooterController::run($this->context);
    }

    private function formIndex (array $formConfig) : void {
        $form = new Form($this->db, $formConfig);
        $entries = $form->getAllEntries();

        ///
        /// View: List of entries
        ///

        $smarty = $this->context->templateEngine;

        $keys = $form->getIndexKeys();

        $url_base = $form->getSlug();
        $items = Vector::from($entries)
            ->map(function ($entry) use ($url_base) {
                return $entry["data"] + [
                    "url" => $this->buildUrl(
                        $url_base . "/" . $entry["metadata"]["document_id"]
                    ),
                ];
            });

        // Header
        $smarty->assign("PAGE_TITLE", $form->getName());
        HeaderController::run($this->context);

        // Body
        $smarty->display("apps/_blocks/page_header.tpl");

        $smarty->assign("keys", $keys);
        $smarty->assign("items", $items);
        $smarty->display("apps/_crud/list.tpl");

        // Footer
        FooterController::run($this->context);
    }

    private function formEntry (array $formConfig, string $document_id) {
        $form = new Form($this->db, $formConfig);
        $entry = $form->getEntry($document_id);
        $title = $entry->guessTitle();

        $content = $entry->getContent()
            ->set("🗓️ Filing date", $entry->getDate());

        ///
        /// View: Form entry
        ///

        $smarty = $this->context->templateEngine;

        // Header
        $smarty->assign('PAGE_TITLE', $form->getName());
        $smarty->assign('custom_css', "dd {white-space: pre-line;}");
        HeaderController::run($this->context);

        // Body
        $smarty->display("apps/_blocks/page_header.tpl");

        echo '<div class="row"><h2>', $title, '</h2></div>';
        $smarty->assign('items', $content);
        $smarty->display("apps/_blocks/dl.tpl");

        if ($entry->hasAttachments()) {
            $smarty->assign("alert_level", "info");
            $smarty->assign("alert_note", "📎 This form has documents attached.");
            $smarty->display("apps/_blocks/alert.tpl");
        }

        $view_url = $form->getOrbeonBaseUrl() . "/view/" . $document_id;
        echo '<div class="row">';
        echo '<p>➕ <a href="' . $view_url . '">View full form on Orbeon</a></p>';
        echo '<p>↩ <a href="' . $this->buildUrl($form->getSlug()) . '">Back to list</a></p>';
        echo '</div>';

        // Footer
        FooterController::run($this->context);
    }

    ///
    /// Controller handler
    ///

    public function handleRequest () {
        /** @var ApplicationConfiguration $config */
        $config = $this->context->configuration;
        $forms = Vector::from($config->forms);

        $argc = count($this->context->url);

        if ($argc == 1) {
            $this->index($forms);
            return;
        }

        $form = $this->get_form_config($this->context->url[1]);
        if ($form->isNone()) {
            // URL points to a non-existing form
            ErrorPageController::show($this->context, 404);
            exit;
        }

        if ($argc == 2) {
            $this->formIndex($form->getValue());
            exit;
        }

        if ($argc == 3) {
            $document_id = $this->context->url[2];

            if (ctype_xdigit($document_id)) {
                // URL points to a form entry
                $this->formEntry($form->getValue(), $this->context->url[2]);
                exit;
            }
        }

        // Unknown URL
        ErrorPageController::show($this->context, 404);
        exit;
    }

    private function buildUrl (string $slug) : string {
        return "/" . Vector::from([
            $this->context->workspace->code,
            $this->context->configuration->bind,
            $slug,
        ])->implode("/");
    }

    private function redirectTo (mixed $slug) : never {
        $url = $this->buildUrl($slug);

        header("Location: $url");
        exit;
    }

    /**
     * @return Option<array<string, mixed>>
     */
    private function get_form_config (string $slug) : Option {
        /** @var ApplicationConfiguration $config */
        $config = $this->context->configuration;

        foreach ($config->forms as $form) {
            if ($form["slug"] == $slug) {
                return new Some($form);
            }
        }

        return new None;
    }

}
