(function () {
    'use strict';

    angular
        .module('app')
        .controller('SettingController', SettingController);

    SettingController.$inject = ['$rootScope', 'SettingService'];
    function SettingController($rootScope, SettingService) {
        var vm = this;

        vm.updateSettings = updateSettings;

        vm.settings = {};

        (function initController() {
            $rootScope.bodyClass = 'page page-settings';
            $rootScope.pageTitle = 'Settings';

            getAllSettings();
        })();

        function getAllSettings() {
            SettingService.getAll(function(response){
                vm.settings = response.data.data;

                /*Convert type of some settings*/
                vm.settings.default_language = parseInt(vm.settings.default_language);
            });
        }

        function updateSettings()
        {
            SettingService.update(vm.settings, function(response){
                getAllSettings();
            });
        }
    }
})();