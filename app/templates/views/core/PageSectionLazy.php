<div id="<?=$id?>"><div class="document-loader"></div></div>
<script type="text/javascript">
    var data = [];
    bindTabHandler('<?=$class?>', $('#<?=$id?>'), function () {
        $.get('<?=$url?>', {}, function(data) {
            $('#<?=$id?>').html(data);
            completeUIExt($('#<?=$id?>'));
            $("#modal-form").parent().position({
                my: "center",
                at: "center",
                of: window
            });
        });
    });
</script>