$(document).ready(function()
{
    // hide custom dictionaries when dialog is open
    $(document).on( "dialogopen", function( event, ui ) {
        $('[id*=fieldRowDict]').hide();
    });

    // display only specific dictionary when Feature field value is changed
    $(document).on( "autocompleteselect", function( event, ui )
    {
        if ( ui.item && event.target.getAttribute('id') == "FunctionText" )
        {
            // hide dictionaries before display only corresponding one
            $('[id*=fieldRowDict]').hide();

            // check for user selected value
            if ( ui.item.value.search(/test\s1/) != -1 ) {
                $('[id=fieldRowDict1]').show();
            }
            if ( ui.item.value.search(/test\s2/) != -1 ) {
                $('[id=fieldRowDict2]').show();
            }
        }
    });
});