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
        service.updateGlobal = updateGlobal;
        service.update = update;
        service.deletePage = deletePage;

        return service;

        function getAll($params, callback, callbackError) {
            return $http.get(baseApi + 'pages', {
                params: $params || {}
            }).then(callback, callbackError);
        }

        function get($id, $lang, callback, callbackError) {
            return $http.get(baseApi + 'pages/details/' + $id + '/' + $lang).then(callback, callbackError);
        }

        function updateGlobal($id, $data, callback, callbackError) {
            if($id && !isNaN(parseFloat($id)) && isFinite($id))
            {
                return $http.post(baseApi + 'pages/edit-global/' + $id, $data).then(callback, callbackError);
            }
            return $http.post(baseApi + 'pages/edit-global', $data).then(callback, callbackError);
        }

        function update($id, $lang, $data, callback, callbackError) {
            return $http.post(baseApi + 'pages/edit/' + $id + '/' + $lang, $data).then(callback, callbackError);
        }

        function deletePage($id, callback, callbackError) {
            return $http.delete(baseApi + 'pages/delete/' + $id).then(callback, callbackError);
        }
    }
})();