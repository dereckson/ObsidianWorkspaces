        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="sidebar-search">
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="{#Search#}">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </li>
{if isset($current_workspace)}
                <li><a href="{get_url($current_workspace->code)}"><i class="fa fa-dashboard fa-fw"></i> {#Home#}</a></li>
{foreach from=$current_workspace_nav item=nav}
                <li><a href="{get_url($current_workspace->code, $nav.url)}"><i class="fa {if $nav.icon}fa-{$nav.icon}{else}fa-circle-o{/if} fa-fw"></i> {$nav.link}</a></li>
{/foreach}
                <li><a href="{get_url($current_workspace->code, 'help')}"><i class="fa fa-question fa-fw"></i> {#Help#}</a></li>
{else}
                <li><a href="{get_url()}"><i class="fa fa-dashboard fa-fw"></i> {#Home#}</a></li>
                <li><a href="{get_url('help')}"><i class="fa fa-question fa-fw"></i> {#Help#}</a></li>
{/if}
                </ul>
            </div>
        </nav>
