$(document).ready(function() {
    console.log('day Ready');
    //table setup.
    var opts = {
        order: [[3, "desc"]],
        columns: [
            {data: 'Hostname'},
            {
                data: 'in',
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
    $('#daySummary').DataTable(opts);

    $('.dayNav').click(function(evt) {
        //console.log(evt);
        let me = evt.target;
        let d = $(me).attr('data-date');
        updateActiveTab({date: d});
    })
})