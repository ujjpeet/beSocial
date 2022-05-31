$( document ).ready(function() {
    $('#hamburger-menu').on('click', function(){
        $('#pop-up-menu').css("display", "block");
    });
    $('#x').on('click', function(){
        $('#pop-up-menu').css("display", "none");
    });

    $('#image').on('click', function (){
        $('#tooltip').css('display', 'block');
    })
    $('#image').on('mouseleave', function (){
        $('#tooltip').css('display', 'none');
    })
});