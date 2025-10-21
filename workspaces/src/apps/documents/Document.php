<?php

class Document {
    public string $id;

    public string $title;

    public string $date;

    public DocumentType $type;

    public array $refs = [];

    public string $content;
}
