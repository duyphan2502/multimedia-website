(function () {
    'use strict';

    angular
        .module('app')
        .factory('AuthenticationService', AuthenticationService);

    AuthenticationService.$inject = ['$http', '$cookieStore', '$rootScope', 'UserService'];
    function AuthenticationService($http, $cookieStore, $rootScope, UserService) {
        var service = {};

        service.login = login;
        service.setCredentials = setCredentials;
        service.clearCredentials = clearCredentials;

        return service;

        function login(email, password, callback, callbackError)
        {
            return UserService.authUser(email, password, callback, callbackError);
        }

        function setCredentials(email, token) {
            $rootScope.globals = {
                currentUser: {
                    email: email,
                    token: token
                }
            };

            $http.defaults.headers.common['Authorization'] = token;
            $cookieStore.put('globals', $rootScope.globals);

            $rootScope.settings.layout.isLogin = false;
        }

        function clearCredentials() {
            $rootScope.globals = {};
            $cookieStore.remove('globals');
            $http.defaults.headers.common.Authorization = null;

            $rootScope.settings.layout.isLogin = true;
        }
    }
})();