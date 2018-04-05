<?php

session_start();

ini_set('display_errors', '1');
error_reporting(E_ALL);

include '../mautosaas/lib/process/config.php';
include '../mautosaas/lib/process/field.php';
include '../mautosaas/lib/util.php';

try {
    $apppdoconn = new PDOConnection('');
    $con        = null;
    if ($apppdoconn) {
        $con = $apppdoconn->getConnection();
        if ($con == null) {
            throw new Exception($apppdoconn->getDBErrorMsg());
        }
    } else {
        throw new Exception('Not able to connect to DB');
    }
    startTransaction($con);
    $sql                   = 'select date_added,email,domain FROM '.DBINFO::$SIGNUP_DBNAME.'.leads';
    $applist               = getResultArray($con, $sql);
    $numdbs                = sizeof($applist);
    $currenttime           = date('Y-m-d H:i:s');
    $lastupdateddateandtime='';

    for ($i = 0; $i < $numdbs; ++$i) {
        $dateadded = $applist[$i][0];
        $email     = $applist[$i][1];
        $domain    = $applist[$i][2];

        $updateddate=convertUTCtoIST($dateadded);
        $hourdiff   = round((strtotime($currenttime) - strtotime($updateddate)) / 3600, 1);

        if ($hourdiff >= 48) {
            if ($domain != '') {
                $sql       ='select f5 from '.DBINFO::$DBNAME.".applicationlist  where f5='$domain'";
                $domainlist = getResultArray($con, $sql);
                $numdbs     = sizeof($domainlist);
                if ($numdbs == 0) {
                    $sql='delete from '.DBINFO::$SIGNUP_DBNAME.".leads where email ='$email'";
                    execSQL($con, $sql);
                    echo 'Datas Successfully Cleaned Up'.'<br>';
                }
            }
        } else {
            echo 'There Is No Data for Cleaning '.'<br>';
        }
    }
} catch (Exception $ex) {
    $msg = $ex->getMessage();
    echo '<br><br>'.'Error:'.$msg;
}

function convertUTCtoIST($dateadded)
{
    date_default_timezone_set('GMT');
    //display the converted time
    $date=  date('Y-m-d H:i', strtotime('+5 hour +30 minutes', strtotime($dateadded)));

    return $date;
}
