<?php

class CheckUncheckFrame
{
    function draw()
    {
        echo '<div class="btn-group">';
        echo '<a class="btn" href="javascript:" onclick="$(\':checkbox\').attr(\'checked\',true);">'.translate('Выбрать все').'</a> ';
        echo '</div>';

        echo '<div class="btn-group">';
        echo '<a class="btn" href="javascript:" onclick="$(\':checkbox\').attr(\'checked\',false);">'.translate('Снять выделение').'</a>';
        echo '</div>';

        echo '<label></label>';
        echo '<br/>';
    }
}