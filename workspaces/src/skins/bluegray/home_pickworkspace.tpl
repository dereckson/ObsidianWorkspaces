<!-- Workspace selector -->

<style>
@media screen and (max-width: 768px) {
    #workspaces {
        margin-left: 5%;
        margin-right: 5%;
    }
}

@media screen and (min-width: 769px) {
    #workspaces {
        margin-left: 3%;
        margin-right: 3%;
    }
}

#workspaces a:hover {
    text-decoration: none;
}

[class*="block-grid-"] > li {
    padding: 0 3rem 1.11111rem;
}
.workspace-description {
    text-align: justify;
}
</style>

<div class="row">
      <div class="jumbotron">
        <h1><span class="glyphicon glyphicon-th-list"></span> {#PickWorkspace#}</h1>
        <p class="lead">{#PickWorkspaceCallForAction#}</p>
      </div>
</div>

<div class="row">
<ul id="workspaces" class="large-block-grid-3 medium-block-grid-2">
{foreach from=$workspaces item=workspace}
    <li class="workspace">
        <h2><a href="/{$workspace->code}" class="workspace-name">{$workspace->name}</a></h2>
        <p class="workspace-description">{$workspace->description}</p>
    </li>
{/foreach}
</ul>
</div>