(function () {
    'use strict';

    angular
        .module('app')
        .controller('PagesController', PagesController);

    PagesController.$inject = ['$rootScope', '$scope', 'MyHelpers', 'PageService'];
    function PagesController($rootScope, $scope, MyHelpers, PageService) {
        var vm = this;

        vm.pages = [];

        vm.getStatus = getStatus;
        vm.showUpdateField = showUpdateField;
        vm.updateStatus = updateStatus;
        vm.cancelUpdate = cancelUpdate;
        vm.confirmUpdate = confirmUpdate;
        vm.deletePage = deletePage;

        /*Search*/
        vm.handleSearch = handleSearch;
        vm.clearSearch = clearSearch;
        vm.allParams = {
            page: 1,
            per_page: 10
        };

        /*Fast edit page*/
        vm.isEditing = false;
        vm.currentSelectedItem = null;
        vm.fastEditData = {};

        /*Pagination*/
        vm.paginationChanged = paginationChanged;
        vm.perPageChanged = perPageChanged;
        //vm.allParams.page = 1;
        vm.totalItems = 0;
        vm.maxSize = 3;
        vm.lastPage = 1;

        /*Group actions*/
        vm.multiSelect = multiSelect;
        vm.changeSelect = changeSelect;
        vm.handleGroupActions = handleGroupActions;
        vm.selectedItems = {};
        vm.checkedAllItems = false;
        vm.drCurrentGroupAction = null;
        vm.groupActions = [
            {
                id: null,
                text: 'Select...'
            },
            {
                id: 'disable',
                text: 'Disable these pages'
            },
            {
                id: 'active',
                text: 'Active these pages'
            }
        ];

        (function initController() {
            $rootScope.bodyClass = 'page page-pages';
            $rootScope.pageTitle = 'All pages';

            getAllPages({
                page: vm.allParams.page,
                per_page: vm.allParams.per_page
            });

            $scope.$on('$viewContentLoaded', function () {
                App.initComponents();
            });
        })();

        function getAllPages($params, callback) {
            $rootScope.showLoadingState();

            vm.selectedItems = {};
            vm.checkedAllItems = false;

            PageService.getAll($params, function (response) {
                /*Successful*/
                if ($params.per_page < 1) {
                    vm.pages = response.data.data;

                    vm.allParams.page = 1;
                    vm.totalItems = vm.pages.length;
                    vm.lastPage = 1;
                }
                else {
                    vm.pages = response.data.data.data;

                    vm.allParams.page = response.data.data.current_page;
                    vm.allParams.per_page = response.data.data.per_page;
                    vm.totalItems = response.data.data.total;
                    vm.lastPage = response.data.data.last_page;
                }

                App.initComponents();
                $rootScope.hideLoadingState();

                /*Callback*/
                if (callback) callback();
            }, function (response) {
                $rootScope.hideLoadingState();
            });
        }

        /*Handle search*/
        function handleSearch() {
            vm.allParams.page = 1;
            getAllPages(vm.allParams);
        }
        function clearSearch() {
            vm.allParams.global_title = undefined;
            perPageChanged();
        }

        /*Pagination*/
        function paginationChanged() {
            getAllPages(vm.allParams);
        }
        /*Change items per page*/
        function perPageChanged() {
            vm.allParams.page = 1;
            getAllPages(vm.allParams);
        }

        /*Get status*/
        function getStatus(status) {
            switch (status) {
                case 0:
                {
                    return '<span class="label label-default label-sm">disabled</span>';
                }
                    break;
                case 1:
                {
                    return '<span class="label label-success label-sm">activated</span>';
                }
                    break;
                default:
                {
                    return '<span class="label label-default label-sm">disabled</span>';
                }
                    break;
            }
        }

        /*Handle fast edit*/
        function showUpdateField(item) {
            vm.isEditing = true;
            vm.currentSelectedItem = item;
            vm.fastEditData = {
                global_title: item.global_title
            }
        }
        function cancelUpdate() {
            vm.isEditing = false;
            vm.currentSelectedItem = null;
            vm.fastEditData = {};
        }
        function confirmUpdate() {
            updatePage(vm.currentSelectedItem.id, vm.fastEditData);
        }

        /*Update page*/
        function updatePage($id, $data) {
            PageService.updateGlobal($id, $data, function (response) {
                getAllPages(vm.allParams);
                vm.isEditing = false;
                vm.currentSelectedItem = null;
                vm.fastEditData = {};
                MyHelpers.showNotification8(response.data.message, 'success');
            }, function (response) {
                MyHelpers.showNotification8(response.data.message, 'error');
            });
        }

        /*Update status*/
        function updateStatus($id, $status) {
            var $data = {
                status: $status
            };
            PageService.updateGlobal($id, $data, function (response) {
                getAllPages(vm.allParams);
                MyHelpers.showNotification8(response.data.message, 'success');
            }, function (response) {
                MyHelpers.showNotification8(response.data.message, 'error');
            });
        }

        /*Delete page*/
        function deletePage($id) {
            PageService.deletePage($id, function (response) {
                getAllPages(vm.allParams);
                MyHelpers.showNotification8(response.data.message, 'success');
            }, function (response) {
                MyHelpers.showNotification8(response.data.message, 'error');
            });
        }

        /*Multi select pages*/
        function multiSelect() {
            vm.selectedItems = MyHelpers.multiSelectDataTable(vm.checkedAllItems, vm.pages, vm.selectedItems);
        }
        /*When user select all items => change scope checkedAllItems to true*/
        function changeSelect() {
            MyHelpers.changeSelectDataTable(vm.pages, vm.selectedItems, function () {
                vm.checkedAllItems = true;
            }, function () {
                vm.checkedAllItems = false;
            });
        }
        /*Handle group actions*/
        function handleGroupActions() {
            PageService.updateGlobal(null, {
                is_group_action: true,
                group_action: vm.drCurrentGroupAction,
                ids: MyHelpers.getGroupActionsSelectedIds(vm.selectedItems)
            }, function (response) {
                getAllPages(vm.allParams);
                MyHelpers.showNotification8(response.data.message, 'success');
            }, function (response) {
                MyHelpers.showNotification8(response.data.message, 'error');
            });
        }
    }
})();