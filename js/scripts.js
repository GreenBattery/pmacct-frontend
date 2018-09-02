// based on github gist: https://gist.github.com/trevershick/737205b0ba79d8877a43
function formatBytes(bytes) {
    if (isNaN(parseFloat(bytes)) || !isFinite(bytes)) return '-';
    var precision = 2;

    if (bytes <= 0){
        return "0 kB"
    }
    var units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB'],
        number = Math.floor(Math.log(bytes) / Math.log(1024));


    return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];


}