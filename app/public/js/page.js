$( document ).ready(function() {
    $('#hamburger-menu').on('click', function(){
        //$('#pop-up-menu').css("display", "block");
        $('#pop-up-menu').show('slow');
    });
    $('#x').on('click', function(){
        //$('#pop-up-menu').css("display", "none");
        $('#pop-up-menu').hide('slow');
    });

    $('#image').on('click', function (){
        $('#tooltip').css('display', 'block');
    })
    $('#image').on('mouseleave', function (){
        $('#tooltip').css('display', 'none');
    })
});