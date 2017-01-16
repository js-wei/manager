var InitiateSimpleDataTable = function () {
    return {
        init: function () {
            var oTable = $('#simpledatatable').dataTable({
                "sDom": "Tflt",
                "iDisplayLength": 10,
                "oTableTools": {
                    "aButtons": [
                        "copy", "csv", "xls","pdf"
                    ],
                    "sSwfPath": "//cdn.bootcss.com/datatables-tabletools/2.1.5/swf/copy_csv_xls_pdf.swf"
                },
                "language": {
                    "search": "",
                    "sLengthMenu": "_MENU_",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next"
                    }
                },
                "aoColumns": [
                  { "bSortable": false },
                  null,
                  { "bSortable": false },
                  null,
                  { "bSortable": false }
                ],
                "aaSorting": []
            });
           
            jQuery('#simpledatatable .group-checkable').change(function () {
                var set = $(".checkboxes");
                var checked = jQuery(this).is(":checked");
                jQuery(set).each(function () {
                    if (checked) {
                        $(this).prop("checked", true);
                        $(this).parents('tr').addClass("active");
                    } else {
                        $(this).prop("checked", false);
                        $(this).parents('tr').removeClass("active");
                    }
                });

            });
            jQuery('#simpledatatable tbody tr .checkboxes').change(function () {
                $(this).parents('tr').toggleClass("active");
            });
           $('#simpledatatable_length').remove();
           $('.DTTT.btn-group').css('right','0px');
        }
    };
}();