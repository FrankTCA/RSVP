<?php
function validate_date($str, $format = 'm-d-Y') {
    $d = DateTime::createFromFormat($format, $str);
    return $d && $d->format($format) === $str;
}