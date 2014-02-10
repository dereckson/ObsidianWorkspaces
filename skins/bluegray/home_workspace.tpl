<header class="row">
    <h1 class="page-header workspace-name">{$current_workspace->name}</h1>
    <p class="workspace-description">{$current_workspace->description}</p>
</header>
{if isset($disclaimers)}
<section class="row" id="disclaimers">
    <h2>{#Disclaimers#}</h2>
{foreach $disclaimers item=disclaimer}
    <div class="disclaimer" id="disclaimer-{$disclaimer->id}">
        <h3 class="disclaimer-title">{$disclaimer->title}</h3>
        <p class="disclaimer-content">{$disclaimer->text|nl2br}</p>
    </div>
{/foreach}
</section>
{/if}
