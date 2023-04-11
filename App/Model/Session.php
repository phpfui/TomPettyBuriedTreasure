<?php

namespace App\Model;

class Session extends \PHPFUI\Session
	{
	public const DEBUG_BAR = 4;

	public static function destroy() : void
		{
		if (PHP_SESSION_ACTIVE == \session_status())
			{
			foreach ($_SESSION as $key => $value)
				{
				unset($_SESSION[$key]);
				}
			$params = \session_get_cookie_params();
			\session_destroy();
			}
		}

	public static function getDebugging(int $flags = 0) : int
		{
		$debug = $_SESSION['debugging'] ?? 0;

		if ($flags)
			{
			return $debug & $flags;
			}

		return $debug;
		}

	public static function setDebugging(int $debug) : void
		{
		if ($debug)
			{
			$_SESSION['debugging'] = $debug;
			}
		else
			{
			unset($_SESSION['debugging']);
			}
		}
	}
