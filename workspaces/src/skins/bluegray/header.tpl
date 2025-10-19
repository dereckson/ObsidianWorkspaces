<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{#SiteTitle#}{if $PAGE_TITLE} :: {$PAGE_TITLE}{/if}</title>
    <link href="{#StaticContentURL#}/css/bootstrap.css" rel="stylesheet">
    <link href="{#StaticContentURL#}/css/font-awesome.min.css" rel="stylesheet">
    <link href="{#StaticContentURL#}/css/bluegray.css" rel="stylesheet">
    <link href="{#StaticContentURL#}/favicon.ico" rel="shorcut icon" type="image/x-icon">
    <link href="{#StaticContentURL#}/favicon.png" rel="icon" type="image/png" />
</head>
<body>
    <a href="#content" class="sr-only">{#SkipNavigation#}</a>
{if isset($custom_workspace_header)}

    <!-- Workspace header-->
{$custom_workspace_header}
{/if}
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">{#ToggleNavigation#}</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
{if isset($current_workspace)}
                <a class="navbar-brand" href="{$current_workspace_url}">{$current_workspace->name}</a>
{else}
                <a class="navbar-brand" href="{$root_url}">{#SiteTitle#}</a>
{/if}
            </div>

            <ul class="nav navbar-top-links navbar-right">
{if $workspaces_count > 1}
                <!-- Workspaces -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-book fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
{foreach from=$workspaces item=workspace}
                        <li>
                            <a href="/{$workspace->code}">
                                <div>
                                    <strong class="workspace-name">{$workspace->name}</strong>
                                    <span class="pull-right text-muted">
                                        <em class="workspace-counter">{counter}</em>
                                    </span>
                                </div>
                                <div class="workspace-description">{$workspace->description}</div>
                            </a>
                        </li>
                        <li class="divider"></li>
{/foreach}
                        <li>
                            <a class="text-center" href="#">
                                <strong>{#WorkspacesManagement#}</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
{/if}
                <!-- Other right navigation actions -->
                <li>
{if isset($current_workspace)}
                        <a href="{$current_workspace_url}?action=user.logout">
{else}
                        <a href="{$root_url}?action=user.logout">
{/if}

                        <i class="fa fa-sign-out fa-fw"></i> {#Logout#}
                    </a>
                </li>
            </ul>
        </nav>

{if isset($controller_custom_nav) }
{include file=$controller_custom_nav}
{else}
{include file='nav_main.tpl'}
{/if}

        <div id="content">