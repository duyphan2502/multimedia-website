(function () {
    'use strict';

    angular
        .module('app')
        .factory('SettingService', SettingService);

    SettingService.$inject = ['$http'];
    function SettingService($http) {
        var service = {};

        service.getAll = getAll;
        service.update = update;

        return service;

        function getAll(callback, callbackError) {
            return $http.get(baseApi + 'settings').then(callback, callbackError);
        }

        function update($data, callback, callbackError) {
            return $http.put(baseApi + 'settings/update-all', $data).then(callback, callbackError);
        }
    }
})();