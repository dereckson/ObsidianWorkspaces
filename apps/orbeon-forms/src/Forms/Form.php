<?php

namespace Waystone\Apps\OrbeonForms\Forms;

use Keruald\Database\Engines\PDOEngine;
use Keruald\OmniTools\Collections\HashMap;
use Keruald\OmniTools\Collections\Vector;
use Waystone\Workspaces\Engines\Exceptions\WorkspaceException;

class Form {

    ///
    /// Private members
    ///

    private string $name;

    private string $slug;

    private string $view;

    private string $orbeon_base_url;

    private array $fields;

    /**
     * @var string[]
     */
    private array $index;

    private PDOEngine $db;

    ///
    /// Constructor
    ///

    public function __construct (PDOEngine $db, array $config) {
        $this->db = $db;

        $this->name = $config["name"];
        $this->slug = $config["slug"];
        $this->view = $config["view"];
        $this->orbeon_base_url = $config["orbeon_base_url"];
        $this->fields = $config["fields"];
        $this->index = $config["index"] ?? [];

        $this->validate();
    }

    ///
    /// Getters
    ///

    public function getName () : string {
        return $this->name;
    }

    public function getSlug () : string {
        return $this->slug;
    }

    public function getView () : string {
        return $this->view;
    }

    public function getOrbeonBaseUrl () : string {
        return $this->orbeon_base_url;
    }

    public function getRawFields () : Vector {
        return Vector::from(array_keys($this->fields));
    }

    public function getFields () : HashMap {
        return HashMap::from($this->fields);
    }

    /**
     * @return Vector<string>
     */
    public function getIndexKeys () : Vector {
        return Vector::from($this->index)
            ->map(fn ($key) => $this->fields[$key]);
    }

    ///
    /// Interact with the form entries
    ///

    /**
     * @return iterable<array<string, array<string, string>>>
     */
    public function getAllEntries () : iterable {
        $this->validate();

        $sql = $this->buildSelectQuery();

        $result = $this->db->query($sql);
        foreach ($result as $row) {
            yield $this->entryFromRow($row);
        }

        return [];
    }

    public function getEntry (string $documentId) : Entry {
        return new Entry($this->db, $this, $documentId);
    }

    ///
    /// Enrich SQL content
    ///

    private function entryFromRow (array $row) : array {
        $entry = [
            "data" => [],
            "metadata" => [],
        ];

        foreach ($row as $key => $value) {
            if (str_starts_with($key, "metadata_")) {
                $key = substr($key, 9);
                $entry["metadata"][$key] = $value;
            } else {
                $key = $this->fields[$key];
                $entry["data"][$key] = $value;
            }
        }

        return $entry;
    }

    ///
    /// SQL helper methods
    ///

    private function buildSelectQuery () : string {
        $sql  = "SELECT ";
        $sql .= $this->getQueryFields()->implode(", ");
        $sql .= " FROM ";
        $sql .= $this->view;
        $sql .= " ORDER BY metadata_created DESC";

        return $sql;
    }

    private function getIndexFields() : array {
        if ($this->index) {
            return $this->index;
        }

        // If omitted, use all fields
        return array_keys($this->fields);
    }

    private function getQueryFields () : Vector {
        $fields = $this->getIndexFields();
        $fields[] = "metadata_document_id";
        $fields[] = "metadata_created";

        return Vector::from($fields);
    }

    ///
    /// Validate configuration syntax
    ///

    const string RE_DB_ITEM_EXPRESSION = "/^[a-z0-9_]+$/";

    public function validate () : void {
        $fields = array_keys($this->fields);

        foreach ($fields as $field) {
            if (!$this->validateDatabaseItem($field)) {
                throw new WorkspaceException("Invalid database item expression: $field");
            }
        }

        foreach ($this->index as $field) {
            if (!in_array($field, $fields)) {
                throw new WorkspaceException("Invalid index field: $field (must be declared into fields too)");
            }
        }

        if (!$this->validateDatabaseItem($this->view)) {
            throw new WorkspaceException("Invalid view name: $this->view");
        }
    }

    private function validateDatabaseItem ($expression) : bool {
        // Expression can only contain [a-z0-9_]
        return preg_match(self::RE_DB_ITEM_EXPRESSION, $expression);
    }

}
