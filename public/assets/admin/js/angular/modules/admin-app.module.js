(function () {
    'use strict';
    angular.module('app', []);
    angular
        .module('adminApp', [
            'app',
            'ui.router',
            'ngCookies',
            'ngResource',
            'ngMessages',
            'ui.bootstrap',
            'ngSanitize',
            'mwl.confirm',
            'ng'
        ])
        .config(configHttp)
        .config(config)
        .run(run);

    configHttp.$inject = ['$httpProvider'];
    function configHttp($httpProvider) {
        $httpProvider.defaults.headers.common['Authorization'] = null;
        /*Always send ajax*/
        $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        /*Catch 401 error => return to login page*/
        $httpProvider.interceptors.push(['$q', '$location', '$rootScope', 'MyHelpers', function ($q, $location, $rootScope, MyHelpers) {
            return {
                'request': function(config) {
                    $rootScope.showLoadingState();
                    return config || $q.when(config);
                },
                'response': function(response) {
                    $rootScope.hideLoadingState();
                    return response || $q.when(response);
                },
                'requestError': function(rejection) {
                    $rootScope.hideLoadingState();
                    return $q.reject(rejection);
                },
                'responseError': function(rejection) {
                    $rootScope.hideLoadingState();
                    if (rejection.status === 401) {
                        MyHelpers.showNotification8('Your session time out. Please login to continue', 'error');
                        $location.path('login');
                    }
                    return $q.reject(rejection);
                }
            };
        }]);
    }

    config.$inject = ['$stateProvider', '$urlRouterProvider'];
    function config($stateProvider, $urlRouterProvider) {
        // Redirect any unmatched url
        $urlRouterProvider.otherwise("/dashboard");

        $stateProvider
            .state('login', {
                url: "/login",
                templateUrl: viewsUrl + "auth/login.template.html",
                controller: "AuthController",
                controllerAs: "vm"
            })
            .state('dashboard', {
                url: "/dashboard",
                templateUrl: viewsUrl + "dashboard/dashboard.template.html",
                controller: "DashboardController",
                controllerAs: "vm"
            })
            .state('pages', {
                url: "/pages",
                templateUrl: viewsUrl + "pages/pages.template.html",
                controller: "PagesController",
                controllerAs: "vm"
            })
            .state('pageDetails', {
                url: "/pages/{id:[0-9]*}/{lang:[0-9]*}",
                templateUrl: viewsUrl + "pages/page-details.template.html",
                controller: "PageDetailsController",
                controllerAs: "vm"
            })
            .state('categories', {
                url: "/categories",
                templateUrl: viewsUrl + "categories/categories.template.html",
                controller: "CategoriesController",
                controllerAs: "vm"
            })
            .state('categoryDetails', {
                url: "/categories/{id:[0-9]*}/{lang:[0-9]*}",
                templateUrl: viewsUrl + "categories/category-details.template.html",
                controller: "CategoryDetailsController",
                controllerAs: "vm"
            })
            .state('settings', {
                url: "/settings",
                templateUrl: viewsUrl + "settings/settings.template.html",
                controller: "SettingController",
                controllerAs: "vm"
            });
    }

    run.$inject = ['$rootScope', '$location', '$cookieStore', '$http', 'SettingsFactory', 'SettingService'];
    function run($rootScope, $location, $cookieStore, $http, SettingsFactory, SettingService) {
        SettingsFactory.defineSettings();

        var $localStorageGlobal = localStorage.globals;
        if($localStorageGlobal)
        {
            $localStorageGlobal = JSON.parse(localStorage.globals);
        }

        // keep user logged in after page refresh
        $rootScope.globals = $cookieStore.get('globals') || $localStorageGlobal || {};
        if ($rootScope.globals.currentUser)
        {
            $http.defaults.headers.common['Authorization'] = $rootScope.globals.currentUser.token;
            $rootScope.settings.layout.isLogin = false;

            SettingService.getAll(function(response){
                $rootScope.cmsSettings = response.data.data;
            });
        }

        $rootScope.$on('$locationChangeStart', function (event, next, current) {
            var restrictedPage = $.inArray($location.path(), ['/login', '/register']) === -1;
            var loggedIn = $rootScope.globals.currentUser;
            if (restrictedPage && !loggedIn)
            {
                $location.path('/login');
            }
        });

        $rootScope.$on('$stateChangeSuccess', function(){
            $rootScope.settings.layout.loading = false;
            Layout.setSidebarMenuActiveLink('match');
        });

        /*Show - Hide loading state*/
        $rootScope.toggleLoadingState = function()
        {
            $rootScope.settings.layout.loading = !$rootScope.settings.layout.loading;
        };
        $rootScope.showLoadingState = function()
        {
            $rootScope.settings.layout.loading = true;
        };
        $rootScope.hideLoadingState = function()
        {
            $rootScope.settings.layout.loading = false;
        };
    }
})();