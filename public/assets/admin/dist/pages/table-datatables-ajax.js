var TableDatatablesAjax = function () {

    var handleRecords = function (options) {

        var grid = new Datatable();

        grid.init({
            src: options.src || $("#datatable_ajax"),
            onSuccess: options.onSuccess || function(grid, response){

            },
            onError: options.onError || function (grid) {

            },
            onDataLoad: options.onDataLoad || function(grid) {

            },
            loadingMessage: 'Loading...',
            dataTable: {
                "bStateSave": options.saveOnCookie || true, // save datatable state(pagination, sort, etc) in cookie.

                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": options.defaultPageLength || 10, // default record count per page
                "ajax": {
                    "url": options.ajaxGet || null
                },
                "order": [
                    [1, "asc"]
                ]
            }
        });

        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                grid.setAjaxParam("customActionType", "group_action");
                grid.setAjaxParam("customActionName", action.val());
                grid.setAjaxParam("id", grid.getSelectedRows());
                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
            } else if (action.val() == "") {
                Utility.showNotification('Please select an action', 'danger');
            } else if (grid.getSelectedRowsCount() === 0) {
                Utility.showNotification('No record selected', 'warning');
            }
        });

        //grid.setAjaxParam("customActionType", "group_action");
        //grid.getDataTable().ajax.reload();
        //grid.clearAjaxParams();
    };

    return {
        init: function (options) {
            handleRecords(options);
        }
    };
}();