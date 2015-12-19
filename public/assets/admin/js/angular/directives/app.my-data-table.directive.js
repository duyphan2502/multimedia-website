(function () {
    'use strict';

    angular
        .module('app')
        .directive('myDataTable', myDataTable);

    function myDataTable() {
        return {
            restrict: 'A', //E = element, A = attribute, C = class, M = comment
            transclude: true,
            scope: {
                drTotalItems: '=',
                drItemsPerPage: '=',
                drCurrentPage: '=',
                drMaxSize: '=',
                drPaginationChanged: '&',
                drPerPageChanged: '&',
                drSelectedItems: "=",
                drGroupActions: '=',
                drCurrentGroupAction: '=',
                drHandleGroupActions: '&',
                drDisablePageChange: '@',
                drDisableGroupAction: '@'
            },
            link: function (scope, elm, attr, model) {

            },
            templateUrl: templatesUrl + 'directives/my-data-table.directive.html',
            controller: myDataTableController,
            controllerAs: 'dr'
        };
    }

    myDataTableController.$inject = ['$scope', 'MyHelpers'];
    function myDataTableController($scope, MyHelpers) {
        var dr = this;

        dr.disablePageChange = (typeof $scope.drDisablePageChange != 'undefined') ? $scope.drDisablePageChange : 'false';
        dr.disableGroupAction = (typeof $scope.drDisableGroupAction != 'undefined') ? $scope.drDisableGroupAction : 'false';

        /*Getting some helpers*/
        dr.countSelected = MyHelpers.countSelected;

        /*Pagination*/
        dr.onPaginationChanged = onPaginationChanged;
        dr.onPerPageChanged = onPerPageChanged;

        /*Group actions*/
        dr.handleGroupActions = handleGroupActions;
        dr.changeGroupActions = changeGroupActions;

        (function initController() {

        })();

        function onPaginationChanged() {
            setTimeout(function () {
                $scope.drPaginationChanged();
            }, 100);
        }

        function onPerPageChanged() {
            setTimeout(function () {
                $scope.drPaginationChanged();
            }, 100);
        }

        function handleGroupActions() {
            setTimeout(function () {
                $scope.drHandleGroupActions();
            }, 100);
        }

        function changeGroupActions(action) {
            $scope.drCurrentGroupAction = action;
        }
    }
})();