(function () {
    'use strict';

    angular
        .module('app')
        .directive('categoryList', categoryList);

    function categoryList() {
        return {
            restrict: 'EA', //E = element, A = attribute, C = class, M = comment
            require: '?ngModel',
            scope: {
                //@ reads the attribute value, = provides two-way binding, & works with functions
                drClass: '@',
                drName: '@',
                drLabel: '@',
                drChange: '&',
                bindModel: '=ngModel'
            },
            templateUrl: templatesUrl + 'directives/category-list.directive.html',
            controller: categoryListDirectiveController,
            controllerAs: 'dr',
            link: function (scope, element, attrs, model) {

            } //DOM manipulation
        };
    }

    categoryListDirectiveController.$inject = ['$scope', 'CategoryService'];
    function categoryListDirectiveController($scope, CategoryService)
    {
        var dr = this;

        dr.categories = [];

        (function initController() {
            getAllCategory();
        })();

        function getAllCategory()
        {
            CategoryService.getAll({}, function(response){
                dr.categories = response.data.data;
            },function(response){
                dr.categories = [];
            });
        }
    }
})();