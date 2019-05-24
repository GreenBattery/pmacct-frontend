var tabXhr = null;

$(function() {


    //updateActiveTab();
    //tab handling
    updateActiveTab()




    $('.nav-tabs a').on('show.bs.tab', function(evt) {
        let href = evt.target.href;
        console.log('show event triggered: ' + href);
        href = href.split('#')[1]; //we oonly want the part after the #
        location.hash = href;
        updateActiveTab();
    });

});

function updateActiveTab() {
    let hash = location.hash;
    let action = 'day';
    if (hash.length !== 0) {
        console.log(hash);
        hash = hash.split('#')[1];
        action = hash;
    }else {
        console.log('handling first tab');
        let t = $('.nav-tabs a:first');
        let href= $(t).attr('href');
        href = href.split('#')[1]; //only want the part after #.
        action = href;
    }

    location.hash = action;


    console.log('retrieving content for: ' + action);
    if (tabXhr !== null) {
        tabXhr.abort(); //cancel any currently ongoing requests.
    }
    let data = {action: action}
    tabXhr = $.ajax({
        url: '/stats.php',
        method: 'GET',
        data: data,
        success: function(data, status, xhr) {
            let handle = '#' + action;
            $(handle).fadeOut(300);
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