<header class="row">
    <div class="page-header">
        <h1>{$documentId}</h1>
        <h2 class="document-name">{$document->title}</h2>
    </div>
</header>
<section class="row">
    <div class="document-metadata col-md-3 col-md-offset-5 col-lg-offset-9">
        <p><strong>{#Date#}{#_t#}</strong> {$document->date}</p>
        <p><strong>{#Type#}{#_t#}</strong> {$documentType}</p>
{if $document->refs}
        <p><strong>{#RefsUs#}{#_t#}</strong> {$document->refs->us}</p>
        <p><strong>{#RefsYours#}{#_t#}</strong> {$document->refs->yours}</p>
{/if}
    </div>
</section>
<section class="row">
    <div class="document-content">
        {$document->content|nl2br}
    </div>
</section>