(function () {
    'use strict';

    angular
        .module('app')
        .factory('MyHelpers', MyHelpersFactory);

    MyHelpersFactory.$inject = ['$rootScope'];
    function MyHelpersFactory($rootScope) {
        var service = {};

        /*Group actions - data tables*/
        service.countSelected = countSelected;
        service.getGroupActionsSelectedIds = getGroupActionsSelectedIds;
        service.changeSelectDataTable = changeSelectDataTable;
        service.multiSelectDataTable = multiSelectDataTable;


        /*Notific8*/
        service.showNotification8 = showNotification8;

        /*Bas64 encode*/
        service.Base64 = Base64;

        return service;

        /*----BEGIN handle group action - use for directive my data tables----*/
        /*Count selected items*/
        function countSelected(obj) {
            var size = 0, key;
            for (key in obj) {
                if (obj.hasOwnProperty(key) && obj[key] == true) size++;
            }
            return size;
        }

        /*Get selected ids from group actions*/
        function getGroupActionsSelectedIds(items) {
            var ids = [], data = {};
            for (var key in items) {
                if (items.hasOwnProperty(key) && items[key] == true) {
                    ids.push(parseInt(key));
                }
            }
            return ids;
        }

        /*When user change ids checkbox in my-data-table*/
        function changeSelectDataTable(pages, selectedItems, callback, callbackFalse) {
            if (pages.length == countSelected(selectedItems)) {
                callback();
            }
            else {
                callbackFalse();
            }
        }

        /*When user choose all items*/
        function multiSelectDataTable(isCheckedAllItem, $pages, selectedItems) {
            if (!isCheckedAllItem) {
                selectedItems = {};
            }
            else {
                var selectedAllPages = {};
                for (var i = 0; i < $pages.length; i++) {
                    var $id = $pages[i].id;
                    selectedAllPages[$id] = true;
                }
                selectedItems = selectedAllPages;
            }
            return selectedItems;
        }

        /*----END handle group action - use for directive my data tables----*/

        /*SOME PLUGINS*/
        /*Notific8 plugin*/
        function showNotification8($message, $type) {
            switch($type) {
                case 'success':
                {
                    $type = 'lime';
                } break;
                case 'info':
                {
                    $type = 'teal';
                } break;
                case 'warning':
                {
                    $type = 'tangerine';
                } break;
                case 'danger':
                {
                    $type = 'ruby';
                } break;
                case 'error':
                {
                    $type = 'ruby';
                } break;
                default:
                {
                    $type = 'ebony';
                } break;
            }
            $.notific8('zindex', 11500);

            if($message instanceof Array)
            {
                $message.forEach(function(value){
                    $.notific8($.trim(value), {
                        theme: $type,
                        sticky: false,
                        horizontalEdge: 'top',
                        verticalEdge: 'right'
                    });
                });
            }
            else
            {
                $.notific8($.trim($message), {
                    theme: $type,
                    sticky: false,
                    horizontalEdge: 'top',
                    verticalEdge: 'right'
                });
            }
        }

        // Base64 encoding service
        var Base64 = {

            keyStr: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=',

            encode: function (input) {
                var output = "";
                var chr1, chr2, chr3 = "";
                var enc1, enc2, enc3, enc4 = "";
                var i = 0;

                do {
                    chr1 = input.charCodeAt(i++);
                    chr2 = input.charCodeAt(i++);
                    chr3 = input.charCodeAt(i++);

                    enc1 = chr1 >> 2;
                    enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                    enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                    enc4 = chr3 & 63;

                    if (isNaN(chr2)) {
                        enc3 = enc4 = 64;
                    } else if (isNaN(chr3)) {
                        enc4 = 64;
                    }

                    output = output +
                        this.keyStr.charAt(enc1) +
                        this.keyStr.charAt(enc2) +
                        this.keyStr.charAt(enc3) +
                        this.keyStr.charAt(enc4);
                    chr1 = chr2 = chr3 = "";
                    enc1 = enc2 = enc3 = enc4 = "";
                } while (i < input.length);

                return output;
            },

            decode: function (input) {
                var output = "";
                var chr1, chr2, chr3 = "";
                var enc1, enc2, enc3, enc4 = "";
                var i = 0;

                // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
                var base64test = /[^A-Za-z0-9\+\/\=]/g;
                if (base64test.exec(input)) {
                    window.alert("There were invalid base64 characters in the input text.\n" +
                        "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
                        "Expect errors in decoding.");
                }
                input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

                do {
                    enc1 = this.keyStr.indexOf(input.charAt(i++));
                    enc2 = this.keyStr.indexOf(input.charAt(i++));
                    enc3 = this.keyStr.indexOf(input.charAt(i++));
                    enc4 = this.keyStr.indexOf(input.charAt(i++));

                    chr1 = (enc1 << 2) | (enc2 >> 4);
                    chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                    chr3 = ((enc3 & 3) << 6) | enc4;

                    output = output + String.fromCharCode(chr1);

                    if (enc3 != 64) {
                        output = output + String.fromCharCode(chr2);
                    }
                    if (enc4 != 64) {
                        output = output + String.fromCharCode(chr3);
                    }

                    chr1 = chr2 = chr3 = "";
                    enc1 = enc2 = enc3 = enc4 = "";

                } while (i < input.length);

                return output;
            }
        };
    }
})();