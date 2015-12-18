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

        function login(user, callback, callbackError)
        {
            return UserService.authUser(user.email, user.password, callback, callbackError);
        }

        function setCredentials(email, token, remember) {
            if(!remember || remember != true) remember = false;

            $rootScope.globals = {
                currentUser: {
                    email: email,
                    token: token
                }
            };

            $http.defaults.headers.common['Authorization'] = token;
            $cookieStore.put('globals', $rootScope.globals);

            /*Remember user*/
            if(remember)
            {
                localStorage.setItem('globals', JSON.stringify($rootScope.globals));
            }

            $rootScope.settings.layout.isLogin = false;
        }

        function clearCredentials() {
            $rootScope.globals = {};
            $cookieStore.remove('globals');
            localStorage.removeItem('globals');
            $http.defaults.headers.common.Authorization = null;

            $rootScope.settings.layout.isLogin = true;
        }
    }
})();