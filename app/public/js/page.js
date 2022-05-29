$( document ).ready(function() {
    $('#hamburger-menu').on('click', function(){
        $('#menu').css("display", "block");
    });
    $('#x').on('click', function(){
        $('#menu').css("display", "none");
    });
});