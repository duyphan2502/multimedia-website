<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js" ng-app="adminApp"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js" ng-app="adminApp"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" ng-app="adminApp">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title ng-bind="pageTitle + ' | Site vui dashboard'"></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    {{--<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>--}}
    <link href="{{ asset('theme/admin/assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('theme/admin/assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('theme/admin/assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('theme/admin/assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('theme/admin/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('theme/admin/assets/global/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/admin/assets/global/plugins/bootstrap-modal/css/bootstrap-modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/core/third_party/notific8/jquery.notific8.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->

    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{ asset('theme/admin/assets/global/css/components-md.min.css') }}" rel="stylesheet" id="style_components" type="text/css"/>
    <link href="{{ asset('theme/admin/assets/global/css/plugins-md.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END THEME GLOBAL STYLES -->

    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{ asset('theme/admin/assets/layouts/layout/css/layout.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('theme/admin/assets/layouts/layout/css/themes/darkblue.min.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="{{ asset('assets/admin/css/style.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END THEME LAYOUT STYLES -->

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}"/>

    <script type="text/javascript">
        var baseUrl = '{{ asset('') }}',
            assetsUrl  = '{{ asset('assets') }}/',
            templatesUrl  = '{{ asset('templates/admin') }}/',
            viewsUrl  = '{{ asset('views/admin') }}/',
            baseApi = '{{ asset('admin/api') }}/';
    </script>
</head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-md @{{ bodyClass }}"
      ng-class="{'on-loading': settings.layout.loading}">

<!-- Loading state -->
<spinner-bar class="page-spinner-bar"></spinner-bar>
<!-- Loading state -->

<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top"
     ng-controller="HeaderController"
     ng-if="!settings.layout.isLogin"
     ng-include="'/views/admin/inc/header.template.html'"></div>
<!-- END HEADER -->

<!-- BEGIN HEADER & CONTENT DIVIDER -->
<div class="clearfix"></div>
<!-- END HEADER & CONTENT DIVIDER -->

<!-- BEGIN CONTAINER -->
<div class="" ng-class="{'page-container': !settings.layout.isLogin}">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper"
         ng-controller="SidebarController"
         ng-if="!settings.layout.isLogin"
         ng-include="'/views/admin/inc/sidebar.template.html'"></div>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="" ng-class="{'page-content-wrapper': !settings.layout.isLogin}">
        <!-- BEGIN CONTENT BODY -->
        <div class="" ng-class="{'page-content': !settings.layout.isLogin}">
            <!-- BEGIN ACTUAL CONTENT -->
            {{--<h3 class="page-title" ng-if="!settings.layout.isLogin">@{{ pageTitle }}</h3>--}}
            <div ui-view class="fade-in-up"></div>
            <!-- END ACTUAL CONTENT -->
        </div>
        <!-- END CONTENT BODY -->
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->

<!-- BEGIN FOOTER -->
<div class="page-footer"
     ng-controller="FooterController"
     ng-if="!settings.layout.isLogin"
     ng-include="'/views/admin/inc/footer.template.html'"></div>
<!-- END FOOTER -->

<!--[if lt IE 9]>
<script src="{{ asset('theme/admin/assets/global/plugins/respond.min.js') }}"></script>
<script src="{{ asset('theme/admin/assets/global/plugins/excanvas.min.js') }}"></script>
<![endif]-->

<!-- BEGIN CORE PLUGINS -->
<script src="{{ asset('assets/admin/dist/core.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/admin/dist/angular-core.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/admin/core/third_party/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/admin/core/third_party/notific8/jquery.notific8.min.js') }}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ asset('theme/admin/assets/global/scripts/app.js') }}" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->

<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{ asset('assets/admin/dist/app.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/admin/dist/angular-app.min.js') }}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->
</body>

</html>