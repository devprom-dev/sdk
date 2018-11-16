$(document).ready(function()
{
    // hide custom dictionaries when dialog is open
    $(document).on( "dialogopen", function( event, ui ) {
        $('[id*=fieldRowdic_]').hide();
    });

    // display only specific dictionary when Feature field value is changed
    $(document).on( "autocompleteselect", function( event, ui )
    {
        if ( ui.item && event.target.getAttribute('id') == "FunctionText" )
        {
            // hide dictionaries before display only corresponding one
            $('#fieldRowdic_' + ui.item.id).show();

        }
    });
});