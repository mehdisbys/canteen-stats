<?php
/**
 * Removes newlines and extraneous spaces
 * @param $string
 * @return string
 */
 function c($string)
{
    return trim(preg_replace('/\s\s+/', ' ', $string));
}

/**
 * Extracts price from string
 * @param $string
 * @return mixed
 */
function getPrice($string)
{
    return preg_replace('/[^0-9,.]/', '', $string);
}

function getCleanDate($date)
{
    return preg_replace('/[^0-9,.]/', '-', $date);
}