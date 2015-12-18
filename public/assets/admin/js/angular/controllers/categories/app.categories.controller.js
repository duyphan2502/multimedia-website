(function () {
    'use strict';

    angular
        .module('app')
        .controller('CategoriesController', CategoriesController);

    CategoriesController.$inject = ['$rootScope', '$scope', 'MyHelpers', 'CategoryService'];
    function CategoriesController($rootScope, $scope, MyHelpers, CategoryService) {
        var vm = this;

        vm.categories = [];

        vm.getStatus = getStatus;
        vm.showUpdateField = showUpdateField;
        vm.updateStatus = updateStatus;
        vm.cancelUpdate = cancelUpdate;
        vm.confirmUpdate = confirmUpdate;
        vm.deleteCategory = deleteCategory;

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
                text: 'Disable these categories'
            },
            {
                id: 'active',
                text: 'Active these categories'
            }
        ];

        (function initController() {
            $rootScope.bodyClass = 'page page-pages';
            $rootScope.pageTitle = 'All categories';

            getAllCategories({
                page: vm.allParams.page,
                per_page: vm.allParams.per_page
            });

            $scope.$on('$viewContentLoaded', function () {
                App.initComponents();
            });
        })();

        function getAllCategories($params, callback) {
            $rootScope.showLoadingState();

            vm.selectedItems = {};
            vm.checkedAllItems = false;

            CategoryService.getAll($params, function (response) {
                /*Successful*/
                if ($params.per_page < 1) {
                    vm.categories = response.data.data;

                    vm.allParams.page = 1;
                    vm.totalItems = vm.categories.length;
                    vm.lastPage = 1;
                }
                else {
                    vm.categories = response.data.data.data;

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
            getAllCategories(vm.allParams);
        }
        function clearSearch() {
            vm.allParams.global_title = undefined;
            perPageChanged();
        }

        /*Pagination*/
        function paginationChanged() {
            getAllCategories(vm.allParams);
        }
        /*Change items per page*/
        function perPageChanged() {
            vm.allParams.page = 1;
            getAllCategories(vm.allParams);
        }

        /*Get status*/
        function getStatus(status) {
            switch (status) {
                case 0:
                {
                    return '<span class="label label-default">disabled</span>';
                }
                    break;
                case 1:
                {
                    return '<span class="label label-success">activated</span>';
                }
                    break;
                default:
                {
                    return '<span class="label label-default">disabled</span>';
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
            updateCategory(vm.currentSelectedItem.id, vm.fastEditData);
        }

        /*Update page*/
        function updateCategory($id, $data) {
            CategoryService.updateGlobal($id, $data, function (response) {
                getAllCategories(vm.allParams);
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
            CategoryService.updateGlobal($id, $data, function (response) {
                getAllCategories(vm.allParams);
                MyHelpers.showNotification8(response.data.message, 'success');
            }, function (response) {
                MyHelpers.showNotification8(response.data.message, 'error');
            });
        }

        /*Delete page*/
        function deleteCategory($id) {
            CategoryService.deleteCategory($id, function (response) {
                getAllCategories(vm.allParams);
                MyHelpers.showNotification8(response.data.message, 'success');
            }, function (response) {
                MyHelpers.showNotification8(response.data.message, 'error');
            });
        }

        /*Multi select pages*/
        function multiSelect() {
            vm.selectedItems = MyHelpers.multiSelectDataTable(vm.checkedAllItems, vm.categories, vm.selectedItems);
        }
        /*When user select all items => change scope checkedAllItems to true*/
        function changeSelect() {
            MyHelpers.changeSelectDataTable(vm.categories, vm.selectedItems, function () {
                vm.checkedAllItems = true;
            }, function () {
                vm.checkedAllItems = false;
            });
        }
        /*Handle group actions*/
        function handleGroupActions() {
            CategoryService.updateGlobal(null, {
                is_group_action: true,
                _group_action: vm.drCurrentGroupAction,
                ids: MyHelpers.getGroupActionsSelectedIds(vm.selectedItems)
            }, function (response) {
                getAllCategories(vm.allParams);
                MyHelpers.showNotification8(response.data.message, 'success');
            }, function (response) {
                MyHelpers.showNotification8(response.data.message, 'error');
            });
        }
    }
})();