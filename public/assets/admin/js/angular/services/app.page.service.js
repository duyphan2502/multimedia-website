(function () {
    'use strict';

    angular
        .module('app')
        .factory('PageService', PageService);

    PageService.$inject = ['$http'];
    function PageService($http) {
        var service = {};

        service.getAll = getAll;
        service.get = get;
        service.update = update;

        return service;

        function getAll(callback, callbackError) {
            return $http.get(baseApi + 'pages').then(callback, callbackError);
        }

        function get($id, $lang, callback, callbackError) {
            return $http.get(baseApi + 'pages/details/' + $id + '/' + $lang).then(callback, callbackError);
        }

        function update($id, $lang, $data, callback, callbackError) {
            return $http.post(baseApi + 'pages/edit/' + $id + '/' + $lang, $data).then(callback, callbackError);
        }
    }
})();