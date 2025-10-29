<?php

namespace Waystone\Apps\OrbeonForms\Forms;

use Waystone\Workspaces\Engines\Exceptions\DocumentNotFoundException;
use Waystone\Workspaces\Engines\Exceptions\WorkspaceException;

use Keruald\Database\Engines\PDOEngine;
use Keruald\OmniTools\Collections\HashMap;
use Keruald\OmniTools\Collections\Vector;

use DateTime;

class Entry {

    ///
    /// Members
    ///

    private Form $form;

    private string $document_id;

    private HashMap $content;

    private DateTime $created;

    private PDOEngine $db;

    ///
    /// Constructors
    ///

    public function __construct (PDOEngine $db, Form $form, string $document_id) {
        $this->form = $form;
        $this->document_id = $document_id;
        $this->db = $db;

        $this->loadFromDatabase();
    }

    ///
    /// Getters
    ///

    public function getContent () : HashMap {
        return $this->content;
    }

    public function getDate () : string {
        return $this->created->format("Y-m-d");
    }

    ///
    /// Helper methods
    ///

    /**
     * Guess the title of the entry, based on the first field.
     * If specific fields are declared for the index, the first of those is used.
     *
     * @return string
     */
    public function guessTitle () : string {
        $field = $this->form
            ->getIndexKeys()
            ->firstOr($this->form->getRawFields()->first());

        return $this->content[$field] ?? "(untitled entry)";
    }

    public function countAttachments () : int {
        $this->validate();

        $sql = "SELECT count(*)
                FROM orbeon_form_data_attach
                WHERE document_id = :document_id";

        return $this->db
            ->prepare($sql)
            ->with("document_id", $this->document_id)
            ->query()
            ->fetchScalar();
    }

    public function hasAttachments () : bool {
        return $this->countAttachments() > 0;
    }

    ///
    /// Load from database
    ///

    private function loadFromDatabase () : void {
        $sql = $this->buildSelectQuery();

        $result = $this->db
            ->prepare($sql)
            ->with("document_id", $this->document_id)
            ->query();

        if (!$result || $result->numRows() === 0) {
            throw new DocumentNotFoundException(
                "Document ID not found: $this->document_id"
            );
        }

        $row = $result->fetchRow();
        $this->created = new DateTime($row["metadata_created"]);
        $this->content = $this->form->getFields()
            ->mapValuesAndKeys(function ($fieldColumn, $fieldName) use ($row) {
                return [$fieldName, $row[$fieldColumn]];
            });
    }

    ///
    /// SQL helper methods
    ///

    private function buildSelectQuery () : string {
        $sql  = "SELECT ";
        $sql .= $this->getQueryFields()->implode(", ");
        $sql .= " FROM ";
        $sql .= $this->form->getView();
        $sql .= " WHERE metadata_document_id = :document_id";

        return $sql;
    }

    private function getQueryFields () : Vector {
        return $this->form->getRawFields()
            ->push("metadata_document_id")
            ->push("metadata_created");
    }

    ///
    /// Validate configuration syntax
    ///

    public function validate () : void {
        $this->form->validate();

        if (!ctype_xdigit($this->document_id)) {
            throw new WorkspaceException(
                "Invalid document ID: $this->document_id"
            );
        }
    }

}
