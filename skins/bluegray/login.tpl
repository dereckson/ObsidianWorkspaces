<!DOCTYPE html>
<html>
<head>
    <title>{#SiteTitle#}{if $WorkspaceName} :: {$WorkspaceName}{/if}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{#StaticContentURL#}/css/bootstrap.css" rel="stylesheet">
    <link href="{#StaticContentURL#}/favicon.ico" rel="shorcut icon" type="image/x-icon">
    <link href="{#StaticContentURL#}/favicon.png" rel="icon" type="image/png" />
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <style>
    @media screen and (max-width: 400px) {
        body {
            background-color: black;
            background-image: url(/images/bg/GrayGlacier-400.jpg), url(/images/bg/GrayGlacier-400-low.jpg);
            background-position: top right;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
        }
    }

    @media screen and (min-width: 401px) and (max-width: 720px) {
        body {
            background-color: black;
            background-image: url(/images/bg/GrayGlacier-720.jpg), url(/images/bg/GrayGlacier-720-low.jpg);
            background-position: top right;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
        }
    }

    @media screen and (min-width: 721px) and (max-width: 1280px) {
        body {
            background-color: black;
            background-image: url(/images/bg/GrayGlacier-1280.jpg), url(/images/bg/GrayGlacier-1280-low.jpg);
            background-position: top right;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
    }

    @media screen and (min-width: 1281px) and (max-width: 1440px) {
        body {
            background-color: black;
            background-image: url(/images/bg/GrayGlacier-1440.jpg), url(/images/bg/GrayGlacier-1440-low.jpg);
            background-position: top right;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
    }

    @media screen and (min-width: 1441px) and (max-width: 1920px) {
        body {
            background-color: black;
            background-image: url(/images/bg/GrayGlacier-1920.jpg), url(/images/bg/GrayGlacier-1920-low.jpg);
            background-position: top right;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
    }

    @media screen and (min-width: 1921px) {
        body {
            background-color: black;
            background-image: url(/images/bg/GrayGlacier-large.jpg), url(/images/bg/GrayGlacier-large-low.jpg);
            background-position: top right;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
    }

    .panel-default {
        opacity: 0.9;
        margin-top:30px;
    }

    .form-group.last { margin-bottom:0px; }

    #product {
        text-align: center;
        padding-top: 1em;
        margin-top: 0;
    }

    #product:before {
        content: "[ ";
    }

    #product:after {
        content: " ]";
    }

    .external-auth-links {
        list-style: none;
    }

    .external-auth-links > li + li {
        margin-top: 1em;
    }
    </style>
</head>
<body>
    <p id="product">{if $WorkspaceName}<strong>{$WorkspaceName}</strong>{else}{#Product#}{/if}</p>

    <div class="container">
{if $PrintInternalLogin}
        <div class="row">
            <div class="col-md-5 col-md-offset-7">
                <div class="panel panel-default" id="internal-login-panel">
                    <div class="panel-heading">
                        <span class="glyphicon glyphicon-lock"></span> {#SiteTitle#}</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{$PostURL}">
                        <div class="form-group">
                            <label for="username" class="col-sm-3 control-label">
                                {#Login#}</label>
                            <div class="col-sm-9">
                                <input name="username" type="text" class="form-control" id="username" value="{$username}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-sm-3 control-label">
                                {#Password#}</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                        </div>
                        <!--
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="keep-logged" id="keep-logged" value="1" />
                                        {#RememberMe#}
                                    </label>
                                </div>
                            </div>
                        </div>
                        -->
                        <div class="form-group last">
                            <div class="col-sm-offset-3 col-sm-9">
                                <input type="submit" id="LogIn" name="LogIn" value="{#OK#}" class="btn btn-primary btn-sm" />
                            </div>
                        </div>
                        </form>
                    </div>
{if $LoginError}
                    <div class="panel-footer">
                        <p>{$LoginError}</p>
                        <p><strong>{#LostPassword#}</strong> Contact your CTP support agent.</p>
                    </div>
{/if}
                </div>
            </div>
        </div>
{/if}
{if $ExternalAuthenticationMethodsNav}
        <div class="row">
            <div class="col-md-5 col-md-offset-7">
                <div class="panel panel-default" id="internal-login-panel">
                    <div class="panel-heading">
                        <span class="glyphicon glyphicon-lock"></span> {#ExternalLogin#}</div>
                    <div class="panel-body">
                        <ul class="external-auth-links">
{foreach $ExternalAuthenticationMethodsNav item=authLink}
                            <li class="external-auth-link"><a href="{$authLink.href}">{$authLink.text}</a></li>
{/foreach}
                        </ul>
                    </div>
{if $ExternalLoginErrors}
                    <div class="panel-footer">
{foreach $ExternalLoginErrors item=externalLoginError}
                        <p>{$externalLoginError}</p>
{/foreach}
                    </div>
{/if}
                </div>
            </div>
        </div>
{/if}
    </div>

    <script src="https://code.jquery.com/jquery.js"></script>
    <script src="{#StaticContentURL#}/js/bootstrap.min.js"></script>
</body>
</html>
