<?php
//ini_set ( "display_errors", "1" );
//error_reporting ( E_ALL );
chdir("/var/www/mauto");


include("../leadsengage/lib/process/config.php");
include("../leadsengage/lib/process/field.php");
include("../leadsengage/lib/util.php");

function displayCronlog($domain,$msg) {
	$logdir="app/logs/$domain";
			if (!is_dir($logdir)) {
				$old = umask(0);
				mkdir($logdir, 0777, true);
				umask($old);
			}
			$logfile = "$logdir/cronmonitor.log";
			$baseurl = "localhost";
			$remoteaddr = "localhost";					
			$logfilesize = getLogFileSize($logfile);
			if ($logfilesize > LOGINFO::$DEFAULT_FILE_SIZE) {
				$filepath = $logfile;
				createLogZipfile($logdir, "qsignup.log");
				if (file_exists($filepath)) {
					$old = umask(0);
					unlink($filepath);
					umask($old);
				}
			}
			$currenttime = date("Y-m-d H:i:s");
			error_log($remoteaddr . " : " . $currenttime . " : $msg\n", 3, $logfile);
		
    }

    if(sizeof($argv) < 2){
     exit("No Arguments Provided!");
    }
$arguments="";
$domainattrfound=false;
for($index=1;$index<sizeof($argv);$index++){
$arguement=$argv[$index];
if (strpos($arguement, '--domain=') !== false) {
$domainattrfound=true;
}
    $arguments.=" ".$arguement;
}
try{
if(!$domainattrfound){
    $pdoconn = new PDOConnection ( "" );
	if ($pdoconn) {
		$con = $pdoconn->getConnection ();
		if ($con == null) {
			throw new Exception ( $pdoconn->getDBErrorMsg () );
		}
	} else {
		throw new Exception ( "Not able to connect to DB" );
    }
    $operation=$argv[1];
    $sql = "select skiplimit from cronmonitorinfo where command='$operation'";
    displayCronlog("general","SQL QUERY:".$sql);
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
        displayCronlog("general","This operation ($operation) already in process.");
        exit(0);
    } else {
        $sql = "insert into cronmonitorinfo values('','$operation','0')";
        displayCronlog("general","SQL QUERY:".$sql);
        $result = execSQL($con, $sql);
    }
    $sql="select domain from applicationlist";
    $domainlist = getResultArray ( $con, $sql );
    //$SKIP_MAX_LIMIT=5;
    for($di=0;$di<sizeof($domainlist);$di++){
        $domain=$domainlist[$di][0];
        $command="php app/console $arguments --domain=$domain";
        displayCronlog($domain,"Command Invoked:".$command);
        $output = shell_exec($command);        
        displayCronlog($domain,"Command Results:".$output);
    }
    cleanCronStatus($con,$operation,"");
}else{
$command="php app/console $arguments ";
displayCronlog("general","Command Invoked:".$command);
$output = shell_exec($command);
displayCronlog("general","Command Results:".$output);       
}
}catch(Exception $ex){
    $msg = $ex->getMessage ();
    displayCronlog("general","Exception Occur:".$msg);
}

function cleanCronStatus($con,$command,$domain){
    $sql = "delete from cronmonitorinfo where  command='$command'";
    displayCronlog("general","SQL QUERY:".$sql);
    $result = execSQL($con, $sql);
    }
?>
