$(document).ready(function() {
    //tooltips
    console.log('month ready');
    //$('[data-toggle="tooltip"]').tooltip();
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });

    //table setup.
    var opts = {
        responsive: true,
        order: [[3, "desc"]],
        deferRender: true,
        autoWidth: false,
        columns: [
            {data: 'Hostname', responsivePriority: 1},
            {
                data: 'in',
                responsivePriority: 3,
                render: function(data, type, row) {
                    if (type === "display") {
                        return formatBytes(data);
                    }else {
                        return data;
                    }

                }
            },
            {
                data: 'out',
                responsivePriority: 4,
                render: function(data, type, row) {
                    if (type === "display") {
                        return formatBytes(data);
                    }else {
                        return data;
                    }

                }
            },
            {
                data: 'total',
                responsivePriority: 2,
                render: function(data, type, row) {
                    if (type === "display") {
                        return formatBytes(data);
                    }else {
                        return data;
                    }

                }
            }
        ],
        'footerCallback': function(row, data, start, end, display) {
            var api = this.api();
            var inTotal = api.column(1).footer().innerText;

            if ($.isNumeric(inTotal)) {
                $(api.column(1).footer()).html(formatBytes(inTotal));
            }


            var outTotal = api.column(2).footer().innerText;
            if ($.isNumeric(outTotal)) {
                $(api.column(2).footer()).html(formatBytes(outTotal));
            }


            var sumTotal = api.column(3).footer().innerText;
            if ($.isNumeric(sumTotal)) {
                $(api.column(3).footer()).html(formatBytes(sumTotal));
            }

        }
    };
    $('#monthSummary').DataTable(opts);

    // Prevent clicks inside IP details collapsible from triggering DataTables row expansion
    $('#monthSummary').on('click', '.ip-details, .ip-details *', function(e) {
        e.stopPropagation();
    });

    $('#monthSummary').on('init.dt', function() {

    });

    $('.monthNav').click(function(evt) {
        //console.log(evt);
        let me = evt.target;
        let mth = $(me).attr('data-month');
        let yr = $(me).attr('data-year');
        updateActiveTab({month: mth, year: yr});
    })
})