var tabXhr = null;

$(function() {
    //tab handling
    let hash = location.hash;
    if (hash.length !== 0) {
        console.log(hash);
        action = hash.split('#')[1];
        window.location.hash = action;
        console.log('showing tab: ' + action);
        $('.nav-tabs a[href="#' + action + '"]').tab('show');
    }else {
        console.log('handling first tab');
        window.location.hash= 'day'
        $('#dayTab').tab('show');
    }


    updateActiveTab()


    $('.nav-tabs a').on('show.bs.tab', function(evt) {
        window.location.hash = evt.target.hash;
        updateActiveTab();
    });

});

function updateActiveTab(args=null) {


    let action = window.location.hash.split('#')[1];
    $('#' + action).html('<div class="container d-flex h-100 align-items-center justify-content-center">' +
        '<div class="row flex-row h-100" >' +
        '<div class="col-2 flex-column h-100"><span class="spinner-border text-info "></span></div></div></div>');


    console.log('retrieving content for: ' + action);
    if (tabXhr !== null) {
        tabXhr.abort(); //cancel any currently ongoing requests.
    }
    let data = {action: action}
    //if any args supplied, add them to data to send.
    if (args != null) {
        for (var k in args) {
            if (args.hasOwnProperty(k)){
                data[k] = args[k];
            }
        }
    }
    tabXhr = $.ajax({
        url: '/stats.php',
        method: 'GET',
        data: data,
        success: function(data, status, xhr) {
            let handle = '#' + action;
            $(handle).hide();
            $(handle).html(data); //update tab contents.
            $(handle).fadeIn(3000);
        },
        complete: function(u, o){
            tabXhr = null;
        }
    });
}

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