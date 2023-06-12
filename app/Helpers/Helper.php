<?php

function format_number($number)
{
    if ($number > 0) {
        return number_format((float)$number, 2, '.', '');
    } else {
        return 0;
    }
}
