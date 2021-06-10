// Loaded after CoreUI app.js

$(function(){

    let alert_box = '\
    <div class="alert alert-success alert-dismissible" id="alert-box"\
        style="display:none; position:absolute; z-index: 100000000000; right: 5vh; top: 8vh;" role="alert">\
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">\
            <span aria-hidden="true">&times;</span>\
        </button>\
        <h5 id="message"></h5>\
    </div>';

    $('body').prepend(alert_box);
});


window.show_alert = function (alert_message, alert_type, fontawesome_icon_class){
    setTimeout(function(){
        $("#alert-box").fadeOut();
    }, 3000);
    
    let icon = "<i class='" + fontawesome_icon_class + "'></i> ";
    $("#alert-box").removeClass("alert-success alert-warning alert-danger").addClass("alert-" + alert_type).fadeIn();
    $("#alert-box #message").html(alert_message);
    $("#alert-box #message").prepend(icon);                                    
}

window.formatDateToYMD = function (date) { 
        var dd = (date.getDate() < 10 ? '0' : '') 
                + date.getDate(); 
                  
        var MM = ((date.getMonth() + 1) < 10 ? '0' : '') 
                + (date.getMonth() + 1); 
        var YYYY = date.getUTCFullYear();
        
        return YYYY +  "-" + MM + "-" + dd; 
}