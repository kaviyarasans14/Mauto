<?php

//ini_set ( "display_errors", "1" );
//error_reporting ( E_ALL );
chdir('/var/www/leadsengagesaas');

include '../leadsengagesaas/lib/process/config.php';
include '../leadsengagesaas/lib/process/field.php';
include '../leadsengagesaas/lib/util.php';
include '../leadsengagesaas/lib/process/createElasticEmailSubAccount.php';
include '../leadsengagesaas/lib/process/createSendGridAccount.php';

$loader = require_once __DIR__.'/app/autoload.php';

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

function displayCronlog($domain, $msg)
{
    $logdir="app/logs/$domain";
    if (!is_dir($logdir)) {
        $old = umask(0);
        mkdir($logdir, 0777, true);
        umask($old);
    }
    $logfile     = "$logdir/cronmonitor.log";
    $baseurl     = 'localhost';
    $remoteaddr  = 'localhost';
    $logfilesize = getLogFileSize($logfile);
    if ($logfilesize > LOGINFO::$DEFAULT_FILE_SIZE) {
        $filepath = $logfile;
        createLogZipfile($logdir, 'qsignup.log');
        if (file_exists($filepath)) {
            $old = umask(0);
            unlink($filepath);
            umask($old);
        }
    }
    $currenttime = date('Y-m-d H:i:s');
    error_log($remoteaddr.' : '.$currenttime." : $msg\n", 3, $logfile);
}

if (sizeof($argv) < 2) {
    exit('No Arguments Provided!');
}
$arguments      ='';
$domainattrfound=false;
for ($index=1; $index < sizeof($argv); ++$index) {
    $arguement=$argv[$index];
    if (strpos($arguement, '--domain=') !== false) {
        $domainattrfound=true;
    }
    $arguments .= ' '.$arguement;
}
try {
    if (!$domainattrfound) {
        $pdoconn = new PDOConnection('');
        if ($pdoconn) {
            $con = $pdoconn->getConnection();
            if ($con == null) {
                throw new Exception($pdoconn->getDBErrorMsg());
            }
        } else {
            throw new Exception('Not able to connect to DB');
        }
        $operation=$argv[1];
        if (isset($argv[1])) {
            $fcolname='';
            if ($operation == 'mautic:import') {
                $fcolname = 'f17';
            } elseif ($operation == 'mautic:segments:update') {
                $fcolname = 'f18';
            } elseif ($operation == 'mautic:campaigns:rebuild') {
                $fcolname = 'f19';
            } elseif ($operation == 'mautic:campaigns:trigger') {
                $fcolname = 'f20';
            } elseif ($operation == 'mautic:emails:send') {
                $fcolname = 'f21';
            } elseif ($operation == 'mautic:email:fetch') {
                $fcolname = 'f26';
            } elseif ($operation == 'mautic:list:update') {
                $fcolname = 'f27';
            } elseif ($operation == 'mautic:reports:scheduler') {
                $fcolname = 'f28';
            }
        } else {
            exit('Please Configure Valid Parameter');
        }
        $sql  = "select skiplimit from cronmonitorinfo where command='$operation'";
//        displayCronlog('general', 'SQL QUERY:'.$sql);
        $monitorinfo = getResultArray($con, $sql);
        if (sizeof($monitorinfo) > 0) {
            /* $skiplimit=$monitorinfo[0][0];
             if($skiplimit >= $SKIP_MAX_LIMIT){
             cleanCronStatus($con,$operation,$domain);
             }else{
             $sql="update cronmonitorinfo set skiplimit=skiplimit+1 where domain='$domain' and command='$operation'";
             displayCronlog($domain,"SQL QUERY:".$sql);
             $result = execSQL($con, $sql);
             }	*/
            displayCronlog('general', "This operation ($operation) already in process.");
            exit(0);
        } else {
            $sql = "insert into cronmonitorinfo values('','$operation','0')";
            displayCronlog('general', 'SQL QUERY:'.$sql);
            $result = execSQL($con, $sql);
        }
        $sql        ='select f5 from applicationlist where '.$fcolname.'=\'1\'';
        $domainlist = getResultArray($con, $sql);
        //$SKIP_MAX_LIMIT=5;
        $errormsg = '';

        displayCronlog('general', 'Sizeof Domains :  '.sizeof($domainlist));
        for ($di=0; $di < sizeof($domainlist); ++$di) {
            $errormsg    = '';
            $domain      =$domainlist[$di][0];
            $currentdate = date('Y-m-d');
            $sql         = "select count(*) from cronerrorinfo where domain = '$domain' and operation = '$operation' and createdtime like '$currentdate%'";
            $errorinfo   = getResultArray($con, $sql);
            if (sizeof($errorinfo) != 0 && $errorinfo[0][0] > 5) {
                displayCronlog('general', "This operation ($operation) for ($domain) is failing repeatedly.");
                continue;
            }
            $command="app/console $arguments --domain=$domain";
            displayCronlog($domain, 'Command Invoked:'.$command);
            $output=executeCommand($command);
            if (strpos($output, 'exception->') !== false) {
                $errormsg = $output;
            }
            //	    displayCronlog('general', $domain.'errorinfo:  '.$errormsg);
            if ($errormsg != '') {
                displayCronlog('general', 'errorinfo:  '.$errormsg);
                updatecronFailedstatus($con, $domain, $operation, $errormsg);
                if ($operation == 'mautic:emails:send' && strpos($errormsg, 'Failed to authenticate on SMTP server with') !== false) {
                    CheckESPStatus($con, $domain);
                }
            }
            displayCronlog('general', $domain.' : '.$command);
            displayCronlog($domain, 'Command Results:'.$output);
        }
        cleanCronStatus($con, $operation, '');
    } else {
        $command="php app/console $arguments ";
        displayCronlog('general', 'Command Invoked:'.$command);
        // $output = shell_exec($command);
        $output=executeCommand($command);
        if (strpos($output, 'exception->') !== false) {
            $errormsg = $output;
        }
        displayCronlog('general', 'Command Results:'.$output);
    }
} catch (Exception $ex) {
    $msg = $ex->getMessage();
    displayCronlog('general', 'Exception Occur:'.$msg);
}

function cleanCronStatus($con, $command, $domain)
{
    $sql = "delete from cronmonitorinfo where  command='$command'";
    displayCronlog('general', 'SQL QUERY:'.$sql);
    $result = execSQL($con, $sql);
}

function updatecronFailedstatus($con, $domain, $operation, $errorinfo)
{
    $currentdate = date('Y-m-d H:i:s');
    $sql         = "insert into cronerrorinfo values ('$domain','$operation','$currentdate','$errorinfo')";
    displayCronlog('general', 'SQL QUERY:'.$sql);
    $result      = execSQL($con, $sql);
}

function executeCommand($command)
{
    $output  ='';
    $process = new Process($command);
    try {
        $process->run();
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        // $output = shell_exec($command);
        $output=$process->getOutput();
    } catch (ProcessFailedException $pex) {
        $output='exception->'.$pex->getMessage();
    } catch (Exception $pex) {
        $output='exception->'.$pex->getMessage();
    } finally {
        $process->clearOutput();
        $process->clearErrorOutput();
    }

    return $output;
}
function CheckESPStatus($con, $domain)
{
    require_once "app/config/$domain/local.php";
    $mailer = $parameters['mailer_transport'];
    $status = true;
    if ($mailer == 'mautic.transport.elasticemail') {
        $apikey = $parameters['mailer_password'];
        if ($apikey != '') {
            $status = checkStatusofElastic($apikey);
        }
    } elseif ($mailer == 'mautic.transport.sendgrid_api') {
        $subusername = $parameters['mailer_user'];
        if ($subusername != '') {
            $status = checkStatus($subusername);
        }
    }
    if (!$status) {
        updateEmailAccountStatus($con, $domain);
    }
}

function updateEmailAccountStatus($con, $domain)
{
    $sql         = "select appid from applicationlist where f5 = '$domain';";
    $appidarr    = getResultArray($con, $sql);
    $appid       = $appidarr[0][0];
    $licenseinfo = $appid.'.licenseinfo';
    $sql         = "update $licenseinfo set app_status = 'Suspended'";
    $result      = execSQL($con, $sql);
    $sql         = "update applicationlist set f21 = 0 where f5 = '$domain'";
    $result      = execSQL($con, $sql);
}
