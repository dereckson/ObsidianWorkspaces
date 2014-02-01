<div class="row">
    <h1 class="page-header">Documents</h1>
{if $documents}
    <ul id="documents">
{foreach $documents item=document}
        <li class="document"><a href="{get_url($current_workspace->code, 'docs', $document)}">{$document}</a></li>
{/foreach}
    </ul>
{else}
    <p>No document currently available.</p>
{/if}
</div>
