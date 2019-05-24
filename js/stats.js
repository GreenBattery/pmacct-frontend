$(function() {
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
});

// based on github gist: https://gist.github.com/trevershick/737205b0ba79d8877a43
function formatBytes(bytes) {
    if (isNaN(parseFloat(bytes)) || !isFinite(bytes)) {
        return '-err-';
    }
    var precision = 2;

    if (bytes <= 0){
        return "0 kB"
    }
    var units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB'],
        number = Math.floor(Math.log(bytes) / Math.log(1024));


    return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];


}