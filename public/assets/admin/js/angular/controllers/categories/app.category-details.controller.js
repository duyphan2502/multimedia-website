(function () {
    'use strict';

    angular
        .module('app')
        .controller('CategoryDetailsController', CategoryDetailsController);

    CategoryDetailsController.$inject = ['$rootScope', '$scope', 'CategoryService', '$state', '$stateParams', 'MyHelpers'];
    function CategoryDetailsController($rootScope, $scope, CategoryService, $state, $stateParams, MyHelpers) {
        var vm = this;

        vm.updatePageContent = updatePageContent;
        vm.changeLanguage = changeLanguage;

        vm.categoryId = parseInt($stateParams.id);
        vm.langId = parseInt($stateParams.lang);
        vm.currentObj = null;

        (function initController() {
            page();

            $scope.$on('$viewContentLoaded', function() {
                App.initComponents();
            });
        })();

        function page()
        {
            $rootScope.bodyClass = 'category page-category-edit';
            $rootScope.pageTitle = 'Edit category';
            getPage();
        }

        function getPage(callback, callbackError)
        {
            CategoryService.get(vm.categoryId, vm.langId, function(response){
                /*Successful*/
                vm.currentObj = response.data.data;
                if(callback) callback();
            }, function(response){
                MyHelpers.showNotification8(response.data.message, 'error');
                $state.go('categories');
                if(callbackError) callbackError();
            });
        }

        function updatePageContent()
        {
            CategoryService.update(vm.categoryId, vm.langId, vm.currentObj, function(response){
                if(vm.categoryId == 0)
                {
                    return $state.go('categoryDetails', {
                        id: response.data.category_id,
                        lang: vm.langId
                    });
                }
                getPage();
                MyHelpers.showNotification8(response.data.message, 'success');
            }, function(response){
                MyHelpers.showNotification8(response.data.message, 'error');
            });
        }

        function changeLanguage()
        {
            setTimeout(function(){
                return $state.go('categoryDetails', {
                    id: vm.categoryId,
                    lang: vm.langId
                });
            }, 100);
        }
    }
})();