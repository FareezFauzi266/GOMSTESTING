<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
function smalllogo()
{
    echo '<img src=" " width="128" height="128" alt="IS-CARE" class="brand-image img-circle elevation-3"
           style="opacity: .8">';
}

function browsertitle()
{
    echo "GOMS";
}

function systemfullname()
{
    echo "Gym Operations Management System (GOMS)";
}

function systemacronym()
{
    echo "GOMS";
}

function copyright()
{
    $start = 2025;
    $currentyear = date("Y");
    $string = "Copyright &copy; ";
    if($start == $currentyear)
        $string = $string . $start;
    else
        $string = $string . $start . "-" . $currentyear;

    echo $string . " GOMS All rights reserved.";
}

?>