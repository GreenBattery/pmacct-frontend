<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{block name=title}{/block}</title>
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <script src="/js/jquery.js"></script>
        {block name=scriptFiles}{/block}
        <script src="/js/bootstrap.js"></script>
        {block name=cssFiles}{/block}

        <style type="text/css">
            {block name=css}{/block}
        </style>
    </head>

    <body class="h-100 mt-100">
        <div class="sticky-top">
            <nav class="navbar navbar-expand navbar-expand-lg navbar-expand-md bg-dark navbar-dark">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link " href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/stats.php">Stats</a></li>
                    <li class="nav-item"><a class="nav-link" href="/firewall.php">Firewall</a></li>
                </ul>
            </nav>
        </div>
        <div class="container mt-100 h-100">
            {block name=body}{/block}
        </div>



    </body>
    <script type="text/javascript">
        $(function() {
            //add active class to active link.
            $('a[href="' + this.location.pathname + '"]').parents('li,ul').addClass('active');
        });
        {block name=script}{/block}
    </script>


</html>