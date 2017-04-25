<?php

	abstract class jsonError
	{
		const Success					= 0;
		const ProcedureNotFound			= 1;
		const InvalidProcedureContext	= 2;
		const ProcedureArgumentMissing	= 3;
		const InvalidProcedureArgument	= 4;
		const InternalError				= 5;
		const DatabaseError				= 6;
		const WrongMethodCall			= 7;
		const WrongRequestFormat		= 8;
		const SecurityAuditFailed		= 9;
	
		static function getErrorString($ErrorCode)
		{
			switch ($ErrorCode)
			{
				case jsonError::Success:
					return '';
				case jsonError::ProcedureNotFound:
					return 'Procedure not defined.';
				case jsonError::InvalidProcedureContext:
					return 'Invalid Procedure Context.';
				case jsonError::ProcedureArgumentMissing:
					return 'Procedure Argument Missing.';
				case jsonError::InvalidProcedureArgument:
					return 'Procedure Argument is Invalid.';
				case jsonError::InternalError:
					return 'Internal Error Encountered';
				case jsonError::DatabaseError:
					return 'Error at database level.';
				case jsonError::WrongMethodCall:
					return 'Wrong Method Call. Try /capabilities method';
				case jsonError::WrongRequestFormat:
					return 'Wrong Request Format.';
				case jsonError::SecurityAuditFailed:
					return 'Security Audit Failed';
				default:
					return 'Undefined Error Code String';
			}
		}
	}

?>