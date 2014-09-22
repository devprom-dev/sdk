$(function(){

    var $sidebar = $('#sidebar');

    $(".fancy-select").each(function(){
        $(this).select2($(this).data());
    });

    $("input[type='checkbox']").iCheck({
        checkboxClass: 'icheckbox_square-grey',
        radioClass: 'iradio_square-grey'
    });

    //need some class to present right after click
    $sidebar.on('show.bs.collapse', function(e){
        e.target == this && $sidebar.addClass('open');
    });

    $sidebar.on('hide.bs.collapse', function(e){
        if (e.target == this) {
            $sidebar.removeClass('open');
            $(".content").css("margin-top", '');
        }
    });

    $("input[type='file']").change(function() {
        $(this).parent().next(".fileinput-name").html($(this).val());
    })

});