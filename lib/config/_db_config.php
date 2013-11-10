<?php
/*==============Start Database Server  Configuration ======*/
if($_SERVER['HTTP_HOST'] =="localhost:8080")
{
	$dbservwrite = "10.0.0.96";
	$dbnamewrite = "minyanville_stage";
	$dbuserwrite = "minyanville";
	$dbpasswrite = "79m8Bc72A4";
	$dbpfxwrite  = "";
	$dbportwrite= 3306;
	
	$dbserv = "10.0.0.96";
	$dbname = "minyanville_stage";
	$dbuser = "minyanville";
	$dbpass = "79m8Bc72A4";
	$dbpfx  = "";
	$dbport= 3306;
	
	$dbLogsservwrite = "10.0.0.96";
	$dbLogsnamewrite = "minyanville_logs";
	$dbLogsuserwrite = "minyanville";
	$dbLogspasswrite = "79m8Bc72A4";
	$dbLogspfxwrite  = "";
	$dbLogsportwrite= 3306;
	
	$dbLogsserv = "10.0.0.96";
	$dbLogsname = "minyanville_logs";
	$dbLogsuser = "minyanville";
	$dbLogspass = "79m8Bc72A4";
	$dbLogspfx  = "";
	$dbLogsport= 3306;	
}
else
{
	$dbservwrite = ":/cloudsql/mediaagility.com:minyanville:minyanville";
	$dbnamewrite = "minyanville_stage_gae";
	$dbuserwrite = "minyanville";
	$dbpasswrite = "pOGcxmuc4DLfuHW";
	$dbpfxwrite  = "";
	$dbportwrite= 3306;
	
	$dbserv = ":/cloudsql/mediaagility.com:minyanville:minyanville";
	$dbname = "minyanville_stage_gae";
	$dbuser = "minyanville";
	$dbpass = "pOGcxmuc4DLfuHW";
	$dbpfx  = "";
	$dbport= 3306;
	
	$dbLogsservwrite = ":/cloudsql/mediaagility.com:minyanville:minyanville";
	$dbLogsnamewrite = "minyanville_logs";
	$dbLogsuserwrite = "minyanville";
	$dbLogspasswrite = "pOGcxmuc4DLfuHW";
	$dbLogspfxwrite  = "";
	$dbLogsportwrite= 3306;
	
	$dbLogsserv = ":/cloudsql/mediaagility.com:minyanville:minyanville";
	$dbLogsname = "minyanville_logs";
	$dbLogsuser = "minyanville";
	$dbLogspass = "pOGcxmuc4DLfuHW";
	$dbLogspfx  = "";
	$dbLogsport= 3306;
}
/*==============End Database Server  Configuration ======*/
?>