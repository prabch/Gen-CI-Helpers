<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function validate_datetime($datetime, string $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $datetime);
    return $d && $d->format($format) == $datetime;
}

function validate_json($string) 
{
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
}

function filter($items, array $array)
{
    $return = array();

    is_array($items) OR $items = array($items);

    foreach ($items as $item)
    {
        if (array_key_exists($item, $array)) $return[$item] = $array[$item];
    }

    return $return;
}

function date_range(string $start, string $end, string $format = 'Y-m-d') 
{
    $array = array();
    $interval = new DateInterval('P1D');

    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    foreach($period as $date) { 
        $array[] = $date->format($format); 
    }

    return $array;
}