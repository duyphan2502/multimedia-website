(function () {
    'use strict';

    angular
        .module('app')
        .controller('PageDetailsController', PageDetailsController);

    PageDetailsController.$inject = ['$rootScope', '$scope', 'PageService', '$state', '$stateParams', 'MyHelpers'];
    function PageDetailsController($rootScope, $scope, PageService, $state, $stateParams, MyHelpers) {
        var vm = this;

        vm.updatePageContent = updatePageContent;
        vm.changeLanguage = changeLanguage;

        vm.pageId = parseInt($stateParams.id);
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
            $rootScope.bodyClass = 'page page-page-edit';
            $rootScope.pageTitle = 'Edit page';
            getPage();
        }

        function getPage(callback, callbackError)
        {
            PageService.get(vm.pageId, vm.langId, function(response){
                /*Successful*/
                vm.currentObj = response.data.data;
                if(callback) callback();
            }, function(response){
                MyHelpers.showNotification8(response.data.message, 'error');
                $state.go('pages');
                if(callbackError) callbackError();
            });
        }

        function updatePageContent()
        {
            PageService.update(vm.pageId, vm.langId, vm.currentObj, function(response){
                if(vm.pageId == 0)
                {
                    return $state.go('pageDetails', {
                        id: response.data.page_id,
                        lang: vm.langId
                    });
                }
                getPage();
                MyHelpers.showNotification8(response.data.message, 'success');
            }, function(response){
                MyHelpers.showNotification8(response.data.message, 'error');
                return $state.go('pageDetails', {
                    id: response.data.page_id,
                    lang: vm.langId
                });
            });
        }

        function changeLanguage()
        {
            setTimeout(function(){
                return $state.go('pageDetails', {
                    id: vm.pageId,
                    lang: vm.langId
                });
            }, 100);
        }
    }
})();