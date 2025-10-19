<div class="row">
    <h1 class="page-header">Documents</h1>
{if $documents}
    <ul id="documents">
{foreach from=$documents item=document}
        <li class="document"><a href="{$docs_url}/{$document}">{$document}</a></li>
{/foreach}
    </ul>
{else}
    <p>No document currently available.</p>
{/if}
</div>
