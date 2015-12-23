<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>elFinder 2.0</title>

    <!-- jQuery and jQuery UI (REQUIRED) -->
    <link rel="stylesheet" type="text/css"
          href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

    <!-- elFinder CSS (REQUIRED) -->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/admin/core/third_party/elfinder/css/elfinder.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/core/third_party/elfinder/css/theme.css') }}">
</head>
<body>
    <!-- Element where elFinder will be created (REQUIRED) -->
    <div id="elfinder"></div>

    <!-- elFinder JS (REQUIRED) -->
    <script src="{{ asset('assets/admin/core/third_party/elfinder/js/elfinder.min.js') }}"></script>

    <!-- elFinder translation (OPTIONAL) -->
    <script src="{{ asset('assets/admin/core/third_party/elfinder/js/i18n/elfinder.vn.js') }}"></script>

    <!-- elFinder initialization (REQUIRED) -->
    <script type="text/javascript" charset="utf-8">
        // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
        $(document).ready(function () {
            $('#elfinder').elfinder({
                url: '{{ asset('/elfinder-class/connector.minimal.php') }}',
                lang: 'vi',
                getFileCallback: function (file) {
                    console.log(file)
                }
            });
        });
    </script>
</body>
</html>
