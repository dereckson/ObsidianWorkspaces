        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="sidebar-search">
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </li>
                    <li>
                        <a href="{get_url()}"><i class="fa fa-dashboard fa-fw"></i> Home</a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-question fa-fw"></i> Support & services<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="{get_url('help', 'new-workspace')}">Create a new workspace</a></li>
                            <li><a href="{get_url('help', 'support')}">Technical support</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-info fa-fw"></i> About Obsidian Workspaces<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="{get_url('help', 'about')}">About</a></li>
                                <li><a href="{get_url('help', 'credits')}">Credits</a></li>
                            </ul>
                    </li>
                </ul>
            </div>
        </nav>
