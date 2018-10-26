<?php

class FieldHoursPositiveNegative extends FieldHours
{
    function getText()
    {
        $class = $this->getValue() > 0 ? "badge-success" : "badge-important";
        return '<span class="badge '.$class.'">' . parent::getText() . '</span>';
    }
}
