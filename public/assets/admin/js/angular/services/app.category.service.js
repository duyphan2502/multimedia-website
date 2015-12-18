(function () {
    'use strict';

    angular
        .module('app')
        .factory('CategoryService', CategoryService);

    CategoryService.$inject = ['$http'];
    function CategoryService($http) {
        var service = {};

        service.getAll = getAll;
        service.get = get;
        service.updateGlobal = updateGlobal;
        service.update = update;
        service.deleteCategory = deleteCategory;

        return service;

        function getAll($params, callback, callbackError) {
            return $http.get(baseApi + 'categories', {
                params: $params || {}
            }).then(callback, callbackError);
        }

        function get($id, $lang, callback, callbackError) {
            return $http.get(baseApi + 'categories/details/' + $id + '/' + $lang).then(callback, callbackError);
        }

        function updateGlobal($id, $data, callback, callbackError) {
            if($id && !isNaN(parseFloat($id)) && isFinite($id))
            {
                return $http.post(baseApi + 'categories/edit-global/' + $id, $data).then(callback, callbackError);
            }
            return $http.post(baseApi + 'categories/edit-global', $data).then(callback, callbackError);
        }

        function update($id, $lang, $data, callback, callbackError) {
            return $http.post(baseApi + 'categories/edit/' + $id + '/' + $lang, $data).then(callback, callbackError);
        }

        function deleteCategory($id, callback, callbackError) {
            return $http.delete(baseApi + 'categories/delete/' + $id).then(callback, callbackError);
        }
    }
})();