<?php

	global $_IPS;
	if (!array_key_exists('ABORT_ON_ERROR',$_IPS)) {
		$_IPS['ABORT_ON_ERROR'] = false;
	}

	// -------------------------------------------------------------------------
	function GetLoggerInstanceID() {
		$instances = IPS_GetInstanceListByModuleID('{B7EAF40F-7E3D-4825-A4E1-F15BBDB55419}');
		if (count($instances) == 0) {
			exit('IPSLogger Instance NOT found !');
		}
		return $instances[0];
	}

	// -------------------------------------------------------------------------
	function IPSLogger_Fat($LogContext, $LogMessage) {
		IPSLogger_LogFat(GetLoggerInstanceID(), $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	function IPSLogger_Err($LogContext, $LogMessage) {
		IPSLogger_LogErr(GetLoggerInstanceID(), $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	function IPSLogger_Wrn($LogContext, $LogMessage) {
		IPSLogger_LogWrn(GetLoggerInstanceID(), $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	function IPSLogger_Not($LogContext, $LogMessage, $Priority=0) {
		IPSLogger_LogNot(GetLoggerInstanceID(), $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	function IPSLogger_Inf($LogContext, $LogMessage) {
		IPSLogger_LogInf(GetLoggerInstanceID(), $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	function IPSLogger_Dbg($LogContext, $LogMessage) {
		IPSLogger_LogDbg(GetLoggerInstanceID(), $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	function IPSLogger_Trc($LogContext, $LogMessage) {
		IPSLogger_LogTrc(GetLoggerInstanceID(), $LogContext, $LogMessage);
	}
	

?>