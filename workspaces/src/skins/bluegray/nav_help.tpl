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
                <li><a href="{$current_workspace_url}"><i class="fa fa-dashboard fa-fw"></i> {#Home#}</a></li>
{else}
                <li><a href="{$root_url}"><i class="fa fa-dashboard fa-fw"></i> {#Home#}</a></li>
{/if}
                    <li>
                        <a href="#"><i class="fa fa-question fa-fw"></i> Support & services<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="{$help_url}/new-workspace">Create a new workspace</a></li>
                            <li><a href="{$help_url}/support">Technical support</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-info fa-fw"></i> About Obsidian Workspaces<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="{$help_url}/about">About</a></li>
                                <li><a href="{$help_url}/credits">Credits</a></li>
                            </ul>
                    </li>
                </ul>
            </div>
        </nav>
