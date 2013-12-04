
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <base href="[[++base_url]]">
    <title>My Forums | MODX Community Forums</title>
    <meta name="title" content="My Forums">
    <meta name="author" content="MODX Systems, LLC">
    <link href="/discuss2/assets/components/discuss2/themes/discuss1/css/theme.css" rel="stylesheet" type="text/css">
    <link href="/discuss2/assets/components/discuss2/themes/discuss1/css/gridset.css" rel="stylesheet" type="text/css">
</head>
<body id="forumbody-home" class="forums">
<!-- NEW masthead 2012 start -->
<header class="masthead">
    <div class="wrapper h-group">
        <div class="f-padinfull f-all m-all">
            <div class="f1-f6 t1-t3 m-all">
                <nav class="l-col_16">
                    <ul class="m-sm_nav_pod">
                        <li><a href="http://modx.com/">Back to MODX.com</a></li>
                        <li><a href="forums/">Forums</a></li>
                        <li><a href="http://rtfm.modx.com/">Docs</a></li>
                        <li><a href="http://tracker.modx.com/">Bugs</a></li>
                        <li><a href="">Blog</a></li>
                    </ul>
                </nav>
                <a class="logo" href="forums/" title="MODX Community Forums">MODX Forums</a>
            </div><!-- left side of masthead -->
            <div class="masthead-right f7-f12 t4-t6 m-all">


                <div class="masthead-login m-login_box h-group">
                    <div class="masthead-title"><strong>Login to MODX</strong> Don't have a MODX.com account? <a href="create-user.html">Create one</a></div>
                    <form class="m-login_block" method="post" action="login.html">
                        <input type="hidden" name="service" value="login" />
                        <input type="hidden" name="discussPlace" value="home" />
                        <div class="f7-f8">
                            <p>
                                <input type="text" name="username" id="login-username">
                                <label class="l-inline" for="login-username">modx.com username</label>
                            </p>
                        </div>
                        <div class="f9-f10">
                            <p>
                                <input type="password" name="password" id="login-password">
                                <label class="l-inline" for="login-password">password</label>
                            </p>
                        </div>
                        <div class="f11-f12">
                            <input class="alt-1-cta" type="submit" value="Login">
                        </div>
                        <div class="group-vis f7-f10 m-login_block_uts">
                            <div class="f7-f8">
                                <a href="">Forgot Login?</a>
                            </div>
                            <div class="f9-f10">
                                <input type="checkbox" name="rememberme" id="dis-login-rememberme" value="1"   />
                                <label for="dis-login-rememberme">Remember Me
                                    <span class="error"></span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</header>
<div class="m-breadcrumbs f-padinfull l-horizontal_nav h-group">
    <nav class="container l-left">
        <ul>
            <li class="last clearfix"><span>My Forums</span></li>

            <li class="end">&nbsp;</li>
        </ul>
    </nav>
    <!-- remove out of breadcrumbs eventually-->
    <div class="l-right m-search">
        <div class="m-search-label l-inline">
            <a href="forums/thread/recent">View Latest Posts</a>
        </div>
        <div class="l-inline">
            <p>or Search:</p>
        </div>
        <form class="l-inline" action="forums/search" method="get" accept-charset="utf-8">
            <label for="search_form_input" class="hidden">Search</label>
            <input id="search_form_input" placeholder="Search keyphrase..." name="s" value="" title="Start typing and hit ENTER" type="text" tabindex="1">
            <input value="Go" type="submit" tabindex="2">
        </form>
    </div>
    <!-- / remove out of breadcrumbs-->
</div>

[[+discuss2.trail]]
[[+discuss2.actions.new_thread]]
<div class="wrapper l-center f-padinfull h-group">
<!-- home.tpl -->
[[+discuss2.subboards:empty=``]]
[[+discuss2.pagination]]
<div class="dis-threads forum-grid category panel-stack">
[[+discuss2.content]]
</div><!-- /dis-threads -->

</div><!--/dis-threads f-all-->
[[+discuss2.pagination]]
[[+discuss2.form:empty=``]]
<!-- below recent--> <!-- /below recent-->
<!-- pag--> <!-- /pag recent-->
<!-- thread actions --><!-- /thread actions -->
<!-- bottom actions --><!-- /bottom actions -->
<!-- / home.tpl -->
</div>

[[$discuss2.footer]]


</body>
</html>