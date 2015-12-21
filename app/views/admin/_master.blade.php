<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>Admin dashboard</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Admin dashboard - Tedozi CMS" name="description"/>
    <meta content="duyphan.developer@gmail.com" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="{{ asset('assets/global/fonts/open-sans/font.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/core/third_party/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/core/third_party/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/core/third_party/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/core/third_party/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/core/third_party/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/admin/core/third_party/bootstrap-modal/css/bootstrap-modal-bs3patch.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/core/third_party/bootstrap-modal/css/bootstrap-modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/core/third_party/notific8/jquery.notific8.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->

    <!-- OTHER PLUGINS -->
    @yield('css')
    <!-- END OTHER PLUGINS -->

    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{ asset('theme/admin/assets/global/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css"/>
    <link href="{{ asset('theme/admin/assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END THEME GLOBAL STYLES -->

    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{ asset('theme/admin/assets/layouts/layout/css/layout.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('theme/admin/assets/layouts/layout/css/themes/darkblue.min.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="{{ asset('assets/admin/css/style.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END THEME LAYOUT STYLES -->

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}"/>

    <script type="text/javascript">
        var baseUrl = '{{ asset('') }}';
    </script>
</head>

<body class="page-header-fixed page-content-white {{ $bodyClass or '' }}">

<!-- Loading state -->
<div class="page-spinner-bar">
    <div class="bounce1"></div>
    <div class="bounce2"></div>
    <div class="bounce3"></div>
</div>
<!-- Loading state -->

<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    @include('admin/shared/_header')
</div>
<!-- END HEADER -->

<!-- BEGIN HEADER & CONTENT DIVIDER -->
<div class="clearfix"></div>
<!-- END HEADER & CONTENT DIVIDER -->

<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        @include('admin/shared/_sidebar')
    </div>
    <!-- END SIDEBAR -->

    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <!-- BEGIN ACTUAL CONTENT -->
            @include('admin/shared/_breadcrumb-and-page-title')

            <div class="fade-in-up">
                @yield('content')
            </div>
            <!-- END ACTUAL CONTENT -->
        </div>
        <!-- END CONTENT BODY -->
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->

<!-- BEGIN FOOTER -->
<div class="page-footer">
    @include('admin/shared/_footer')
</div>
<!-- END FOOTER -->

<!--[if lt IE 9]>
<script src="{{ asset('assets/admin/core/third_party/respond.min.js') }}"></script>
<script src="{{ asset('assets/admin/core/third_party/excanvas.min.js') }}"></script>
<![endif]-->

<!-- BEGIN CORE PLUGINS -->
<script src="{{ asset('assets/admin/dist/core.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/admin/core/third_party/notific8/jquery.notific8.min.js') }}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- OTHER PLUGINS -->
@yield('js')
<!-- END OTHER PLUGINS -->

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ asset('theme/admin/assets/global/scripts/app.js') }}" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->

<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{ asset('assets/admin/dist/app.min.js') }}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->

<!-- JS INIT -->
@yield('js-init')
<!-- JS INIT -->

@include('admin/shared/_flash-messages')

</body>

</html>