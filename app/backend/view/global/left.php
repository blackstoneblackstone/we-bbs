<nav id="sidebar" aria-label="Main Navigation">
    <div class="content-header bg-white-5">
        <a class="font-w600 text-dual" href="/">
            {:get_setting('site_name')}
        </a>
    </div>
    <div class="content-side content-side-full">
        <ul class="nav-main siderbar-nav-main">
            {$_menu|raw}
        </ul>
    </div>
</nav>