(function () {
    'use strict';

    angular
        .module('app')
        .controller('SettingController', SettingController);

    SettingController.$inject = ['$rootScope'];
    function SettingController($rootScope) {
        var vm = this;

        (function initController() {
            $rootScope.bodyClass = 'page page-settings';
            $rootScope.pageTitle = 'Settings';
        })();
    }
})();