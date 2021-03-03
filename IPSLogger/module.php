<?php

class IPSLogger extends IPSModule
{
	private const LOGLEVEL_DISABLED      = 0;
	private const LOGLEVEL_FATAL         = 1;
	private const LOGLEVEL_ERROR         = 2;
	private const LOGLEVEL_WARNING       = 3;
	private const LOGLEVEL_NOTIFICATION  = 4;
	private const LOGLEVEL_INFORMATION   = 5;
	private const LOGLEVEL_DEBUG         = 6;
	private const LOGLEVEL_TRACE         = 7;
	private const LOGLEVEL_ALL           = 8;

	private const LOGTYPE_DISABLED       = 'Disabled';
	private const LOGTYPE_FATAL          = 'Fatal';
	private const LOGTYPE_ERROR          = 'Error';
	private const LOGTYPE_WARNING        = 'Warning';
	private const LOGTYPE_NOTIFICATION   = 'Notification';
	private const LOGTYPE_INFORMATION    = 'Information';
	private const LOGTYPE_DEBUG          = 'Debug';
	private const LOGTYPE_TRACE          = 'Trace';
	private const LOGTYPE_ALL            = 'All';

	private const LF                     =  "\n";

	// -------------------------------------------------------------------------
	public function Create() {
		parent::Create();

		$this->RegisterPropertyInteger ('MessagesContextLen',         12);
		$this->RegisterPropertyString  ('MessagesFormatDate',         'Y-m-d H:i:s');
		$this->RegisterPropertyInteger ('MessagesMicroLen',           4);
		$this->RegisterPropertyString  ('MessagesStyleTable',         'font-family:courier; font-size:11px; ');
		$this->RegisterPropertyString  ('MessagesStyleColumn',        '<colgroup><col width="25px"><col width="40px"><col width="100px"><col width="200px"><col></colgroup>');
		$this->RegisterPropertyBoolean ('MessagesAddNewOnTop',        false);
		$this->RegisterPropertyInteger ('MessagesOutputLimit',        20);

		$this->RegisterAttributeInteger('MessageOutputID',            0);

		//We need to call the RegisterHook function on Kernel READY
		$this->RegisterMessage(0, IPS_KERNELMESSAGE);
	}

	// -------------------------------------------------------------------------
	public function RequestAction($Ident, $Value) {
		$this->SetValue($Ident, $Value);
		switch($Ident) {
			case 'MessagesLogLevel':
			case 'LastMessageLogLevel':
			case 'SymconLogLevel':
				break;
			case 'MessagesClear':
				$this->SetValue('MessagesOutput', '');
				$this->SetValue($Ident, false);
				break;
			case 'LastMessageClear':
				$this->ClearLastMessage();
				$this->SetValue($Ident, false);
				break;
			default:
				throw new Exception("Invalid ident");
		}
	}
	
	// -------------------------------------------------------------------------
	public function ApplyChanges() {
		parent::ApplyChanges();

		if (IPS_GetKernelRunlevel() !== KR_READY) {
			return;
		}

		if (!IPS_VariableProfileExists('IPSLogger.LogLevel')) {
			IPS_CreateVariableProfile('IPSLogger.LogLevel', 1);
		} 
		IPS_SetVariableProfileIcon('IPSLogger.LogLevel', 'Intensity');
		IPS_SetVariableProfileText('IPSLogger.LogLevel', '' /*Prefix*/, '' /*Suffix*/);
		IPS_SetVariableProfileDigits('IPSLogger.LogLevel', 0 /*Digits*/); 
		IPS_SetVariableProfileValues('IPSLogger.LogLevel', 0 /*Min*/, 7 /*Max*/, 0 /*Step*/); 
		IPS_SetVariableProfileAssociation('IPSLogger.LogLevel', 0, $this->Translate('Disabled'), '', -1);
		IPS_SetVariableProfileAssociation('IPSLogger.LogLevel', 1, $this->Translate('Fatal'), '', -1);
		IPS_SetVariableProfileAssociation('IPSLogger.LogLevel', 2, $this->Translate('Error'), '', -1);
		IPS_SetVariableProfileAssociation('IPSLogger.LogLevel', 3, $this->Translate('Warning'), '', -1);
		IPS_SetVariableProfileAssociation('IPSLogger.LogLevel', 4, $this->Translate('Notification'), '', -1);
		IPS_SetVariableProfileAssociation('IPSLogger.LogLevel', 5, $this->Translate('Info'), '', -1);
		IPS_SetVariableProfileAssociation('IPSLogger.LogLevel', 6, $this->Translate('Debug'), '', -1);
		IPS_SetVariableProfileAssociation('IPSLogger.LogLevel', 7, $this->Translate('Trace'), '', -1);
		IPS_SetVariableProfileAssociation('IPSLogger.LogLevel', 8, $this->Translate('All'), '', -1);

		if (!IPS_VariableProfileExists('IPSLogger.Clear')) {
			IPS_CreateVariableProfile('IPSLogger.Clear', 0);
		} 
		IPS_SetVariableProfileIcon('IPSLogger.Clear', 'Close');
		IPS_SetVariableProfileText('IPSLogger.Clear', '' /*Prefix*/, '' /*Suffix*/);
		IPS_SetVariableProfileDigits('IPSLogger.Clear', 0 /*Digits*/); 
		IPS_SetVariableProfileValues('IPSLogger.Clear', 0 /*Min*/, 1 /*Max*/, 0 /*Step*/); 
		IPS_SetVariableProfileAssociation('IPSLogger.Clear', 0, $this->Translate(' '), '', -1);
		IPS_SetVariableProfileAssociation('IPSLogger.Clear', 1, $this->Translate('Clear'), '', -1);


		$this->RegisterVariableString('MessagesOutput', $this->Translate('Messages Output'), '~HTMLBox');
		$this->RegisterVariableInteger('MessagesLogLevel', $this->Translate('Messages LogLevel'), 'IPSLogger.LogLevel');
		$this->RegisterVariableBoolean('MessagesClear', $this->Translate('Messages Clear'), 'IPSLogger.Clear');
		$this->EnableAction("MessagesLogLevel");
		$this->EnableAction("MessagesClear");

		$this->RegisterVariableString('LastMessageOutput', $this->Translate('LastMessage Output'), '~HTMLBox');
		$this->RegisterVariableInteger('LastMessageLogLevel', $this->Translate('LastMessage LogLevel'), 'IPSLogger.LogLevel');
		$this->RegisterVariableBoolean('LastMessageClear', $this->Translate('LastMessage Clear'), 'IPSLogger.Clear');
		$this->EnableAction("LastMessageLogLevel");
		$this->EnableAction("LastMessageClear");

		$this->RegisterVariableInteger('SymconLogLevel', $this->Translate('Symcon LogLevel'), 'IPSLogger.LogLevel');
		$this->EnableAction("SymconLogLevel");
		
		$this->RegisterScript("LastMessageReset", "LastMessageClear", "<?php\n\n IPSLogger_ClearLastMessage(".$this->InstanceID.");\n\n ?>");
	}

	// -------------------------------------------------------------------------
	public function GetValue($Ident)
	{
		return GetValue(@IPS_GetObjectIDByIdent($Ident, $this->InstanceID));
	}

	// -------------------------------------------------------------------------
	private function GetLogTypeShort ($LogType) {
		$LogTypeShort =  array (
			self::LOGTYPE_FATAL				 => 'Fat',
			self::LOGTYPE_ERROR				 => 'Err',
			self::LOGTYPE_WARNING			 => 'Wrn',
			self::LOGTYPE_NOTIFICATION		 => 'Not',
			self::LOGTYPE_INFORMATION		 => 'Inf',
			self::LOGTYPE_DEBUG				 => 'Dbg',
			self::LOGTYPE_TRACE				 => 'Trc'
		);
		return $LogTypeShort[$LogType];
	}

	// -------------------------------------------------------------------------
  private function GetLogTypeStyle ($LogType) {
  		$LogTypeStyle= array (
			self::LOGTYPE_FATAL				 => 'color:#000000;background:#FF6347;',
			self::LOGTYPE_ERROR				 => 'color:#000000;background:#FF0000;',
			self::LOGTYPE_WARNING			 => 'background:#bfbfbf;',
			self::LOGTYPE_NOTIFICATION		 => 'font-weight:bold;',
			self::LOGTYPE_INFORMATION		 => '',
			self::LOGTYPE_DEBUG				 => 'color:#B0C4DE;',
			self::LOGTYPE_TRACE				 => 'color:#808080;'
		);
		return $LogTypeStyle[$LogType];
	}
	
	// -------------------------------------------------------------------------
	private function GetEncodedHtml($s) {
		$source = array("&", "ä", "ö", "ü", "Ä", "Ö", "Ü", "ß", "<", ">", "€", "", "¹", "²", "³");
		$dest = array("&", "ä", "ö", "ü", "Ä", "Ö", "Ü", "ß", "<", ">", "¬", "¹", "&#178", "³");
		$s = str_replace($source, $dest, $s);
		return $s;
	}
	// ---------------------------------------------------------------------------------------------------------------------------
	private function OutSymcon($LogLevel, $LogType, $Context, $Msg) {
		$Out = $LogType.': '.$Msg;
		IPS_LogMessage($Context, $Out);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	private function OutLastMessage($LogLevel, $LogType, $Context, $Msg) {
		$Out = '<div style="'.$this->GetLogTypeStyle($LogType).'">'.$LogType.': '.$Msg.'</div>';
		$this->SetValue('LastMessageOutput', $Out);
	}

	// ---------------------------------------------------------------------------------------------------------------------------
	private function OutMessages($LogLevel, $LogType, $Context, $Msg) {
			if(mb_detect_encoding($Msg, 'UTF-8, ISO-8859-1') !== 'UTF-8')
				$Msg  = htmlentities($Msg, ENT_COMPAT, 'UTF-8');
			$Msg  = str_replace("\n", "<BR> ", $Msg);
			switch ($LogType) {
				case self::LOGTYPE_TRACE:  $Msg = '<DIV style="padding-left:30px;">'.$Msg.'</DIV>'; break;
				case self::LOGTYPE_DEBUG:  $Msg = '<DIV style="padding-left:15px;">'.$Msg.'</DIV>'; break;
				default:                   $Msg = '<DIV>'.$Msg.'</DIV>';
			}

			$CurrentMsgId             = $this->ReadAttributeInteger('MessageOutputID')+1;
			$MsgList                  = $this->GetValue('MessagesOutput');
			$MsgCount                 = $this->ReadPropertyInteger('MessagesOutputLimit');
			$MessagesContextLen       = $this->ReadPropertyInteger('MessagesContextLen');
			$MessagesMicroLen         = $this->ReadPropertyInteger('MessagesMicroLen');
			$MessagesFormatDate       = $this->ReadPropertyString('MessagesFormatDate');
			$MessagesAddNewOnTop      = $this->ReadPropertyBoolean('MessagesAddNewOnTop');

			$TablePrefix   = '<style>.row-highlight tr:hover {
							 background-color: rgba(255, 255, 255, 0.1) !important;
							 color:#808080 !important;}</style>
						<table width="100%" class="row-highlight" style="'.$this->ReadPropertyString('MessagesStyleTable').'">';
			$TablePrefix  .= $this->ReadPropertyString('MessagesStyleColumn');
		
			//IPSymcon-Inf-WinLIRC 2010-12-03 22:09:13.000 Msg ...
			$Out =  '<tr id="'.$CurrentMsgId.'" style="'.$this->GetLogTypeStyle($LogType).'">';
			$Out .=	'<td>IPS</td>';
			$Out .=	'<td>-'.$this->GetLogTypeShort($LogType).'-</td>';
			$Out .=	'<td title="'.$Context.'">'.$this->GetEncodedHtml(substr($Context,0,$MessagesContextLen)).'</td>';
			$Out .=	'<td>'.date($MessagesFormatDate).substr(microtime(),1,$MessagesMicroLen).'</td>';
			$Out .=	'<td>'.$Msg.'</td>';
			$Out .= '</tr>';

			//<table><tr id="1"><td>....</tr></table>
			if ($MessagesAddNewOnTop) {
				if (strpos($MsgList, '</table>') === false) {
					$MsgList = "";
				} else {
					$StrPos1	 = strlen($TablePrefix);
					$StrTmp	  = '<tr id="'.($CurrentMsgId-$MsgCount).'"';
					if (strpos($MsgList, $StrTmp)===false) {
					   $StrPos2 = strpos($MsgList, '</table>');
					} else {
						$StrPos2 = strpos($MsgList, $StrTmp);
					}
					$StrLen	  = $StrPos2 - $StrPos1;
					$MsgList	 = substr($MsgList, $StrPos1, $StrLen);
				}
				$this->SetValue('MessagesOutput', $TablePrefix.$Out.$MsgList.'</table>');
			} else {
				if (strpos($MsgList, '</table>') === false) {
					$MsgList = "";
				} else {
					$StrTmp	  = '<tr id="'.($CurrentMsgId-$MsgCount+1).'"';
					if (strpos($MsgList, $StrTmp)===false) {
						$StrPos = strlen($TablePrefix);
					} else {
						$StrPos	  = strpos($MsgList, $StrTmp);
					}
					$StrLen	  = strlen($MsgList) - strlen('</table>') - $StrPos;
					$MsgList	 = substr($MsgList, $StrPos, $StrLen);
				}
				$this->SetValue('MessagesOutput', $TablePrefix.$MsgList.$Out.'</table>');
			}
			$this->WriteAttributeInteger('MessageOutputID', $CurrentMsgId);
		}


	// ---------------------------------------------------------------------------------------------------------------------------
	private function Out($LogLevel, $LogType, $Context, $Msg) {
		
		// Build Stacktrace
		$StackTxt   = '';
		if ($LogType==self::LOGTYPE_ERROR) {
			$DebugTrace = debug_backtrace();
			foreach ($DebugTrace as $Idx=>$Stack) {
				if (array_key_exists('line', $Stack) and array_key_exists('function', $Stack) and array_key_exists('file', $Stack)) {
					$File	 = str_replace('scripts/', '', str_replace(IPS_GetKernelDir(), '', $Stack['file']));
					$Function = $Stack['function'];
					$Line	 = str_pad($Stack['line'],3,' ', STR_PAD_LEFT);
					$StackTxt  .= self::LF."    $Line in $File (call $Function)";
				} elseif (array_key_exists('function', $Stack)) {
					$StackTxt  .= self::LF.'    in '.$Stack['function'];
				} else {
					$StackTxt  .= self::LF.'    Unknown Stack ...';
				}
			}
		}
		// Normalize Context
		 if (strrpos($Context, '\\') !== false) {
			if (strpos($Context, '.') !== false) {
				$Context = substr($Context, strrpos($Context, '\\')+1, strpos($Context, '.')-strrpos($Context, '\\')-1);
			}
		}
		
		$this->SendDebug($Context, $LogType.': '.$Msg, 0);
		
		// Last Messages
		if ($LogLevel <= $this->GetValue('LastMessageLogLevel')) {
			$this-> OutLastMessage($LogLevel, $LogType, $Context, $Msg);
		}

		// Output Messages
		if ($LogLevel <= $this->GetValue('MessagesLogLevel')) {
			$this-> OutMessages($LogLevel, $LogType, $Context, $Msg);
		}

		// Output Symcon
		if ($LogLevel <= $this->GetValue('SymconLogLevel')) {
			$this-> OutSymcon($LogLevel, $LogType, $Context, $Msg);
		}
	}

	// -------------------------------------------------------------------------
	public function ClearLastMessage() {
		$this->SetValue('LastMessageOutput', '');
	}

	// -------------------------------------------------------------------------
	public function LogFat($LogContext, $LogMessage) {
		$this->Out(self::LOGLEVEL_FATAL, self::LOGTYPE_FATAL, $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	public function LogErr($LogContext, $LogMessage) {
		$this->Out(self::LOGLEVEL_ERROR, self::LOGTYPE_ERROR, $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	public function LogWrn($LogContext, $LogMessage) {
		$this->Out(self::LOGLEVEL_WARNING, self::LOGTYPE_WARNING, $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	public function LogNot($LogContext, $LogMessage) {
		$this->Out(self::LOGLEVEL_NOTIFICATION, self::LOGTYPE_NOTIFICATION, $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	public function LogInf($LogContext, $LogMessage) {
		$this->Out(self::LOGLEVEL_INFORMATION, self::LOGTYPE_INFORMATION, $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	public function LogDbg($LogContext, $LogMessage) {
		$this->Out(self::LOGLEVEL_DEBUG, self::LOGTYPE_DEBUG, $LogContext, $LogMessage);
	}

	// -------------------------------------------------------------------------
	public function LogTrc($LogContext, $LogMessage) {
		$this->Out(self::LOGLEVEL_TRACE, self::LOGTYPE_TRACE, $LogContext, $LogMessage);
	}

}

?>