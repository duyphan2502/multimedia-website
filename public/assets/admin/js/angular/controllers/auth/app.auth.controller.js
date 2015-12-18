(function () {
    'use strict';

    angular
        .module('app')
        .controller('AuthController', AuthController);

    AuthController.$inject = ['$scope', '$rootScope', '$location', 'AuthenticationService', 'SettingService', 'MyHelpers'];
    function AuthController($scope, $rootScope, $location, AuthenticationService, SettingService, MyHelpers) {
        var vm = this;

        vm.login = login;
        vm.showForm = showForm;

        vm.currentForm = 'login';
        vm.remember = false;

        (function initController() {
            AuthenticationService.clearCredentials(); // reset login status

            $rootScope.pageTitle = 'Sign in';
            $rootScope.bodyClass = 'page page-auth page-login';

            $scope.$on('$viewContentLoaded', function() {
                App.initAjax(); // initialize core components
            });
        })();

        function login() {
            AuthenticationService.login({
                email: vm.email,
                password: vm.password
            }, function (response){
                AuthenticationService.setCredentials(vm.email, response.data.access_token, vm.remember);
                SettingService.getAll(function(response){
                    $rootScope.cmsSettings = response.data.data;
                });
                $location.path('/');
            }, function(response){
                MyHelpers.showNotification8(response.data.message, 'error');
            });
        }

        function showForm(type)
        {
            type = type || 'login';
            switch (type)
            {
                case 'login':
                {
                    $rootScope.pageTitle = 'Sign in';
                    $rootScope.bodyClass = 'page page-auth page-login';
                } break;
                case 'register':
                {
                    $rootScope.pageTitle = 'Register';
                    $rootScope.bodyClass = 'page page-auth page-register';
                } break;
                case 'forget-password':
                {
                    $rootScope.pageTitle = 'Sign in';
                    $rootScope.bodyClass = 'page page-auth page-forgot-password';
                } break;
            }
            vm.currentForm = type;
        }
    }
})();