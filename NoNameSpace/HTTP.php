<?php

// CubicleSoft PHP HTTP functions.
// (C) 2014 CubicleSoft.  All Rights Reserved.
class HTTP
	{
	// RFC 3986 delimeter splitting implementation.
	public static function ConvertRelativeToAbsoluteURL($baseurl, $relativeurl)
		{
		$relative = (\is_array($relativeurl) ? $relativeurl : self::ExtractURL($relativeurl));

		if ('' != $relative['host'])
			{
			return self::CondenseURL($relative);
			}
		$base = (\is_array($baseurl) ? $baseurl : self::ExtractURL($baseurl));
		$result = ['scheme' => $base['scheme'],
			'loginusername' => $base['loginusername'],
			'loginpassword' => $base['loginpassword'],
			'host' => $base['host'],
			'port' => $base['port'],
			'path' => '',
			'query' => $relative['query'],
			'fragment' => $relative['fragment'], ];

		if ('' == $relative['path'])
			{
			$result['path'] = $base['path'];
			}
		elseif ('/' == \substr($relative['path'], 0, 1))
			{
			$result['path'] = $relative['path'];
			}
		else
			{
			$abspath = \explode('/', $base['path']);
			\array_pop($abspath);
			$relpath = \explode('/', $relative['path']);

			foreach ($relpath as $piece)
				{
				if ('.' == $piece)
					{
					}
				elseif ('..' == $piece)
					{
					\array_pop($abspath);
					}
				else
					{
					$abspath[] = $piece;
					}
				}
			$abspath = \implode('/', $abspath);

			if ('/' != \substr($abspath, 0, 1))
				{
				$abspath = '/' . $abspath;
				}
			$result['path'] = $abspath;
			}

		return self::CondenseURL($result);
		}

	// Takes a ExtractURL() array and condenses it into a string.
	public static function ExtractURL($url)
		{
		$result = ['scheme' => '',
			'authority' => '',
			'login' => '',
			'loginusername' => '',
			'loginpassword' => '',
			'host' => '',
			'port' => '',
			'path' => '',
			'query' => '',
			'queryvars' => [],
			'fragment' => '', ];
		$url = \str_replace('&amp;', '&', $url);
		$pos = \strpos($url, '#');

		if (false !== $pos)
			{
			$result['fragment'] = \substr($url, $pos + 1);
			$url = \substr($url, 0, $pos);
			}
		$pos = \strpos($url, '?');

		if (false !== $pos)
			{
			$result['query'] = \str_replace(' ', '+', \substr($url, $pos + 1));
			$url = \substr($url, 0, $pos);
			$vars = \explode('&', $result['query']);

			foreach ($vars as $var)
				{
				$pos = \strpos($var, '=');

				if (false === $pos)
					{
					$name = $var;
					$value = '';
					}
				else
					{
					$name = \substr($var, 0, $pos);
					$value = \substr($var, $pos + 1);
					}

				if (! isset($result['queryvars'][\urldecode($name)]))
					{
					$result['queryvars'][\urldecode($name)] = [];
					}
				$result['queryvars'][\urldecode($name)][] = \urldecode($value);
				}
			}
		$url = \str_replace('\\', '/', $url);
		$pos = \strpos($url, ':');
		$pos2 = \strpos($url, '/');

		if (false !== $pos && (false === $pos2 || $pos < $pos2))
			{
			$result['scheme'] = \strtolower(\substr($url, 0, $pos));
			$url = \substr($url, $pos + 1);
			}

		if ('//' != \substr($url, 0, 2))
			{
			$result['path'] = $url;
			}
		else
			{
			$url = \substr($url, 2);
			$pos = \strpos($url, '/');

			if (false !== $pos)
				{
				$result['path'] = \substr($url, $pos);
				$url = \substr($url, 0, $pos);
				}
			$result['authority'] = $url;
			$pos = \strpos($url, '@');

			if (false !== $pos)
				{
				$result['login'] = \substr($url, 0, $pos);
				$url = \substr($url, $pos + 1);
				$pos = \strpos($result['login'], ':');

				if (false === $pos)
					{
					$result['loginusername'] = \urldecode($result['login']);
					}
				else
					{
					$result['loginusername'] = \urldecode(\substr($result['login'], 0, $pos));
					$result['loginpassword'] = \urldecode(\substr($result['login'], $pos + 1));
					}
				}
			$pos = \strpos($url, ']');

			if ('[' == \substr($url, 0, 1) && false !== $pos)
				{
				// IPv6 literal address.
				$result['host'] = \substr($url, 0, $pos + 1);
				$url = \substr($url, $pos + 1);
				$pos = \strpos($url, ':');

				if (false !== $pos)
					{
					$result['port'] = \substr($url, $pos + 1);
					$url = \substr($url, 0, $pos);
					}
				}
			else
				{
				// Normal host[:port].
				$pos = \strpos($url, ':');

				if (false !== $pos)
					{
					$result['port'] = \substr($url, $pos + 1);
					$url = \substr($url, 0, $pos);
					}
				$result['host'] = $url;
				}
			}

		return $result;
		}

	public static function CondenseURL($data)
		{
		$result = '';

		if (isset($data['host']) && '' != $data['host'])
			{
			if (isset($data['scheme']) && '' != $data['scheme'])
				{
				$result = $data['scheme'] . '://';
				}

			if (isset($data['loginusername']) && '' != $data['loginusername'] && isset($data['loginpassword']))
				{
				$result .= \rawurlencode($data['loginusername']) . ('' != $data['loginpassword'] ? ':' . \rawurlencode($data['loginpassword']) : '') . '@';
				}
			elseif (isset($data['login']) && '' != $data['login'])
				{
				$result .= $data['login'] . '@';
				}
			$result .= $data['host'];

			if (isset($data['port']) && '' != $data['port'])
				{
				$result .= ':' . $data['port'];
				}

			if (isset($data['path']))
				{
				$data['path'] = \str_replace('\\', '/', $data['path']);

				if ('/' != \substr($data['path'], 0, 1))
					{
					$data['path'] = '/' . $data['path'];
					}
				$result .= $data['path'];
				}
			}
		elseif (isset($data['authority']) && '' != $data['authority'])
			{
			if (isset($data['scheme']) && '' != $data['scheme'])
				{
				$result = $data['scheme'] . '://';
				}
			$result .= $data['authority'];

			if (isset($data['path']))
				{
				$data['path'] = \str_replace('\\', '/', $data['path']);

				if ('/' != \substr($data['path'], 0, 1))
					{
					$data['path'] = '/' . $data['path'];
					}
				$result .= $data['path'];
				}
			}
		elseif (isset($data['path']))
			{
			if (isset($data['scheme']) && '' != $data['scheme'])
				{
				$result = $data['scheme'] . ':';
				}
			$result .= $data['path'];
			}

		if (isset($data['query']))
			{
			if ('' != $data['query'])
				{
				$result .= '?' . $data['query'];
				}
			}
		elseif (isset($data['queryvars']))
			{
			$data['query'] = [];

			foreach ($data['queryvars'] as $key => $vals)
				{
				if (\is_string($vals))
					{
					$vals = [$vals];
					}

				foreach ($vals as $val)
					{
					$data['query'][] = \urlencode($key) . '=' . \urlencode($val);
					}
				}
			$data['query'] = \implode('&', $data['query']);

			if ('' != $data['query'])
				{
				$result .= '?' . $data['query'];
				}
			}

		if (isset($data['fragment']) && '' != $data['fragment'])
			{
			$result .= '#' . $data['fragment'];
			}

		return $result;
		}

	public static function GetUserAgent($type)
		{
		$type = \strtolower($type);

		if ('ie' == $type)
			{
			$type = 'ie11';
			}

		if ('ie6' == $type)
			{
			return 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022)';
			}
		elseif ('ie7' == $type)
			{
			return 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 6.0)';
			}
		elseif ('ie8' == $type)
			{
			return 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; SLCC1)';
			}
		elseif ('ie9' == $type)
			{
			return 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)';
			}
		elseif ('ie10' == $type)
			{
			return 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)';
			}
		elseif ('ie11' == $type)
			{
			return 'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko';
			}
		elseif ('firefox' == $type)
			{
			return 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0';
			}
		elseif ('opera' == $type)
			{
			return 'Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.16';
			}
		elseif ('safari' == $type)
			{
			return 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2';
			}
		elseif ('chrome' == $type)
			{
			return 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36';
			}

		return '';
		}

	// Reasonably parses RFC1123, RFC850, and asctime() dates.
	public static function GetDateTimestamp($httpdate)
		{
		$timestamp_map = ['jan' => 1,
			'feb' => 2,
			'mar' => 3,
			'apr' => 4,
			'may' => 5,
			'jun' => 6,
			'jul' => 7,
			'aug' => 8,
			'sep' => 9,
			'oct' => 10,
			'nov' => 11,
			'dec' => 12, ];
		$year = false;
		$month = false;
		$day = false;
		$hour = false;
		$min = false;
		$sec = false;
		$items = \explode(' ', \preg_replace('/\s+/', ' ', \str_replace('-', ' ', \strtolower($httpdate))));

		foreach ($items as $item)
			{
			if ('' != $item)
				{
				if (false !== \strpos($item, ':'))
					{
					$item = \explode(':', $item);
					$hour = (int)(\count($item) > 0 ? \array_shift($item) : 0);
					$min = (int)(\count($item) > 0 ? \array_shift($item) : 0);
					$sec = (int)(\count($item) > 0 ? \array_shift($item) : 0);

					if ($hour > 23)
						{
						$hour = 23;
						}

					if ($min > 59)
						{
						$min = 59;
						}

					if ($sec > 59)
						{
						$sec = 59;
						}
					}
				elseif (\is_numeric($item))
					{
					if (\strlen($item) >= 4)
						{
						$year = (int)$item;
						}
					elseif (false === $day)
						{
						$day = (int)$item;
						}
					else
						{
						$year = \substr(\date('Y'), 0, 2) . \substr($item, -2);
						}
					}
				else
					{
					$item = \substr($item, 0, 3);

					if (isset($timestamp_map[$item]))
						{
						$month = $timestamp_map[$item];
						}
					}
				}
			}

		if (false === $year || false === $month || false === $day || false === $hour || false === $min || false === $sec)
			{
			return false;
			}

		return \gmmktime($hour, $min, $sec, $month, $day, $year);
		}

	public static function RetrieveWebpage($url, $options = [])
		{
		$startts = \microtime(true);
		$timeout = ($options['timeout'] ?? false);

		if (! \function_exists('stream_socket_client') && ! \function_exists('fsockopen'))
			{
			return ['success' => false,
				'error' => self::HTTPTranslate("The functions 'stream_socket_client' and 'fsockopen' do not exist."),
				'errorcode' => 'function_check', ];
			}
		// Process the URL.
		$url = \trim($url);
		$url = self::ExtractURL($url);

		if ('http' != $url['scheme'] && 'https' != $url['scheme'])
			{
			return ['success' => false,
				'error' => self::HTTPTranslate("RetrieveWebpage() only supports the 'http' and 'https' protocols."),
				'errorcode' => 'protocol_check', ];
			}
		$secure = ('https' == $url['scheme']);
		$protocol = ($secure ? (isset($options['protocol']) && 'ssl' == \strtolower($options['protocol']) ? 'ssl' : 'tls') : 'tcp');

		if (\function_exists('stream_get_transports') && ! \in_array($protocol, \stream_get_transports()))
			{
			return ['success' => false,
				'error' => self::HTTPTranslate("The desired transport protocol '%s' is not installed.", $protocol),
				'errorcode' => 'transport_not_installed', ];
			}
		$host = \str_replace(' ', '-', self::HeaderValueCleanup($url['host']));

		if ('' == $host)
			{
			return ['success' => false,
				'error' => self::HTTPTranslate('Invalid URL.'), ];
			}
		$port = ((int)$url['port'] ?: ($secure ? 443 : 80));
		$defaultport = ((! $secure && 80 == $port) || ($secure && 443 == $port));
		$path = ('' == $url['path'] ? '/' : $url['path']);
		$query = $url['query'];
		$username = $url['loginusername'];
		$password = $url['loginpassword'];
		// Cleanup input headers.
		if (! isset($options['headers']))
			{
			$options['headers'] = [];
			}
		$options['headers'] = self::NormalizeHeaders($options['headers']);
		// Process the proxy URL (if specified).
		$useproxy = (isset($options['proxyurl']) && '' != \trim($options['proxyurl']));

		if ($useproxy)
			{
			$proxyurl = \trim($options['proxyurl']);
			$proxyurl = self::ExtractURL($proxyurl);
			$proxysecure = ('https' == $proxyurl['scheme']);
			$proxyprotocol = ($proxysecure ? (isset($options['proxyprotocol']) && 'ssl' == \strtolower($options['proxyprotocol']) ? 'ssl' : 'tls') : 'tcp');

			if (\function_exists('stream_get_transports') && ! \in_array($proxyprotocol, \stream_get_transports()))
				{
				return ['success' => false,
					'error' => self::HTTPTranslate("The desired transport proxy protocol '%s' is not installed.", $proxyprotocol),
					'errorcode' => 'proxy_transport_not_installed', ];
				}
			$proxyhost = \str_replace(' ', '-', self::HeaderValueCleanup($proxyurl['host']));
			$proxyport = ((int)$proxyurl['port'] ?: ($proxysecure ? 443 : 80));
			$proxypath = ('' == $proxyurl['path'] ? '/' : $proxyurl['path']);
			$proxyusername = $proxyurl['loginusername'];
			$proxypassword = $proxyurl['loginpassword'];
			// Open a tunnel instead of letting the proxy modify the request (HTTP CONNECT).
			$proxyconnect = (isset($options['proxyconnect']) && $options['proxyconnect'] ? $options['proxyconnect'] : false);

			if ($proxyconnect)
				{
				$proxydata = 'CONNECT ' . $host . ':' . $port . " HTTP/1.1\r\n";

				if (isset($options['headers']['User-Agent']))
					{
					$data .= 'User-Agent: ' . $options['headers']['User-Agent'] . "\r\n";
					}
				$proxydata .= 'Host: ' . $host . ($defaultport ? '' : ':' . $port) . "\r\n";
				$proxydata .= "Proxy-Connection: keep-alive\r\n";

				if ('' != $proxyusername)
					{
					$proxydata .= 'Proxy-Authorization: BASIC ' . \base64_encode($proxyusername . ':' . $proxypassword) . "\r\n";
					}

				if (isset($options['proxyheaders']))
					{
					$options['proxyheaders'] = self::NormalizeHeaders($options['proxyheaders']);
					unset($options['proxyheaders']['Accept-Encoding']);

					foreach ($options['proxyheaders'] as $name => $val)
						{
						if ('Content-Type' != $name && 'Content-Length' != $name && 'Proxy-Connection' != $name && 'Host' != $name)
							{
							$proxydata .= $name . ': ' . $val . "\r\n";
							}
						}
					}
				$proxydata .= "\r\n";

				if (isset($options['debug_callback']))
					{
					$options['debug_callback']('rawproxyheaders', $proxydata, $options['debug_callback_opts']);
					}
				}
			}
		// Process the method.
		if (! isset($options['method']))
			{
			if (isset($options['write_body_callback']) || isset($options['body']))
				{
				$options['method'] = 'PUT';
				}
			elseif (isset($options['postvars']) || (isset($options['files']) && \count($options['files'])))
				{
				$options['method'] = 'POST';
				}
			else
				{
				$options['method'] = 'GET';
				}
			}
		$options['method'] = \preg_replace('/[^A-Z]/', '', \strtoupper($options['method']));
		// Process the HTTP version.
		if (! isset($options['httpver']))
			{
			$options['httpver'] = '1.1';
			}
		$options['httpver'] = \preg_replace('/[^0-9.]/', '', $options['httpver']);
		// Process the request.
		$data = $options['method'] . ' ';
		$data .= ($useproxy && ! $proxyconnect ? $url['scheme'] . '://' . $host . ':' . $port : '') . $path . ('' != $query ? '?' . $query : '');
		$data .= ' HTTP/' . $options['httpver'] . "\r\n";
		// Process the headers.
		if ($useproxy && ! $proxyconnect && '' != $proxyusername)
			{
			$data .= 'Proxy-Authorization: BASIC ' . \base64_encode($proxyusername . ':' . $proxypassword) . "\r\n";
			}

		if ('' != $username)
			{
			$data .= 'Authorization: BASIC ' . \base64_encode($username . ':' . $password) . "\r\n";
			}
		$ver = \explode('.', $options['httpver']);

		if ((int)$ver[0] > 1 || (1 == (int)$ver[0] && (int)$ver[1] >= 1))
			{
			if (! isset($options['headers']['Host']))
				{
				$options['headers']['Host'] = $host . ($defaultport ? '' : ':' . $port);
				}
			$data .= 'Host: ' . $options['headers']['Host'] . "\r\n";
			}
		$data .= "Connection: close\r\n";

		if (isset($options['headers']))
			{
			foreach ($options['headers'] as $name => $val)
				{
				if ('Content-Type' != $name && 'Content-Length' != $name && 'Connection' != $name && 'Host' != $name)
					{
					$data .= $name . ': ' . $val . "\r\n";
					}
				}
			}
		// Process the body.
		$body = '';
		$bodysize = 0;

		if (isset($options['write_body_callback']))
			{
			$options['write_body_callback']($body, $bodysize, $options['write_body_callback_opts']);
			}
		elseif (isset($options['body']))
			{
			if (isset($options['headers']['Content-Type']))
				{
				$data .= 'Content-Type: ' . $options['headers']['Content-Type'] . "\r\n";
				}
			$body = $options['body'];
			$bodysize = \strlen($body);
			unset($options['body']);
			}
		elseif (isset($options['files']) && \count($options['files']))
			{
			$mime = '--------' . \substr(\sha1(\uniqid(\mt_rand(), true)), 0, 25);
			$data .= 'Content-Type: multipart/form-data; boundary=' . $mime . "\r\n";

			if (isset($options['postvars']))
				{
				foreach ($options['postvars'] as $name => $val)
					{
					$name = self::HeaderValueCleanup($name);
					$name = \str_replace('"', '', $name);

					if (\is_string($val) || \is_numeric($val))
						{
						$val = [$val];
						}

					foreach ($val as $val2)
						{
						$body .= '--' . $mime . "\r\n";
						$body .= 'Content-Disposition: form-data; name="' . $name . "\"\r\n";
						$body .= "\r\n";
						$body .= $val2 . "\r\n";
						}
					}
				unset($options['postvars']);
				}
			$bodysize = \strlen($body);
			// Only count the amount of data to send.
			foreach ($options['files'] as $num => $info)
				{
				$name = self::HeaderValueCleanup($info['name']);
				$name = \str_replace('"', '', $name);
				$filename = self::FilenameSafe(self::ExtractFilename($info['filename']));
				$type = self::HeaderValueCleanup($info['type']);
				$body2 = '--' . $mime . "\r\n";
				$body2 .= 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $filename . "\"\r\n";
				$body2 .= 'Content-Type: ' . $type . "\r\n";
				$body2 .= "\r\n";
				$info['filesize'] = (isset($info['datafile']) ? \filesize($info['datafile']) : \strlen($info['data']));
				$bodysize += \strlen($body2) + $info['filesize'] + 2;
				$options['files'][$num] = $info;
				}
			$body2 = '--' . $mime . "--\r\n";
			$bodysize += \strlen($body2);
			}
		else
			{
			if (isset($options['postvars']))
				{
				foreach ($options['postvars'] as $name => $val)
					{
					$name = self::HeaderValueCleanup($name);

					if (\is_string($val) || \is_numeric($val))
						{
						$val = [$val];
						}

					foreach ($val as $val2)
						{
						$body .= ('' != $body ? '&' : '') . \urlencode($name) . '=' . \urlencode($val2);
						}
					}
				unset($options['postvars']);
				}

			if ('' != $body)
				{
				$data .= "Content-Type: application/x-www-form-urlencoded\r\n";
				}
			$bodysize = \strlen($body);
			}

		if ($bodysize < \strlen($body))
			{
			$bodysize = \strlen($body);
			}
		// Finalize the headers.
		if ($bodysize || '' != $body || 'POST' == $options['method'])
			{
			$data .= 'Content-Length: ' . $bodysize . "\r\n";
			}
		$data .= "\r\n";

		if (isset($options['debug_callback']))
			{
			$options['debug_callback']('rawheaders', $data, $options['debug_callback_opts']);
			}
		// Finalize the initial data to be sent.
		$data .= $body;
		$bodysize -= \strlen($body);
		$body = '';
		$result = ['success' => true,
			'rawsendsize' => 0,
			'rawrecvsize' => 0,
			'rawrecvheadersize' => 0,
			'startts' => $startts, ];
		$debug = (isset($options['debug']) && $options['debug']);

		if ($debug)
			{
			$result['rawsend'] = '';
			$result['rawrecv'] = '';
			}

		if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
			{
			\fclose($fp);

			return ['success' => false,
				'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
				'errorcode' => 'timeout_exceeded', ];
			}
		// Connect to the target server.
		$errornum = 0;
		$errorstr = '';

		if ($useproxy)
			{
			if (! isset($options['proxyconnecttimeout']))
				{
				$options['proxyconnecttimeout'] = 10;
				}
			$timeleft = self::GetTimeLeft($startts, $timeout);

			if (false !== $timeleft)
				{
				$options['proxyconnecttimeout'] = \min($options['proxyconnecttimeout'], $timeleft);
				}

			if (! \function_exists('stream_socket_client'))
				{
				$fp = @\fsockopen($proxyprotocol . '://' . $proxyhost, $proxyport, $errornum, $errorstr, $options['proxyconnecttimeout']);
				}
			else
				{
				$context = @\stream_context_create();

				if ($proxysecure && isset($options['proxysslopts']) && \is_array($options['proxysslopts']))
					{
					self::ProcessSSLOptions($options, 'proxysslopts', $host);

					foreach ($options['proxysslopts'] as $key => $val)
						{
						@\stream_context_set_option($context, 'ssl', $key, $val);
						}
					}
				$fp = @\stream_socket_client($proxyprotocol . '://' . $host . ':' . $port, $errornum, $errorstr, $options['proxyconnecttimeout'], STREAM_CLIENT_CONNECT, $context);
				$contextopts = \stream_context_get_options($context);

				if ($proxysecure && isset($options['proxysslopts']) && \is_array($options['proxysslopts']) && ('ssl' == $protocol || 'tls' == $protocol) && isset($contextopts['ssl']['peer_certificate']))
					{
					if (isset($options['debug_callback']))
						{
						$options['debug_callback']('proxypeercert', @\openssl_x509_parse($contextopts['ssl']['peer_certificate']), $options['debug_callback_opts']);
						}
					}
				}

			if (false === $fp)
				{
				return ['success' => false,
					'error' => self::HTTPTranslate("Unable to establish a connection to '%s'.", ($proxysecure ? $proxyprotocol . '://' : '') . $proxyhost . ':' . $proxyport),
					'info' => $errorstr . ' (' . $errornum . ')',
					'errorcode' => 'proxy_connect', ];
				}
			$result['connected'] = \microtime(true);

			if ($proxyconnect)
				{
				// Send the HTTP CONNECT request.
				\fwrite($fp, $proxydata);

				if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
					{
					\fclose($fp);

					return ['success' => false,
						'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
						'errorcode' => 'timeout_exceeded', ];
					}
				$result['rawsendsize'] += \strlen($proxydata);
				$result['rawsendproxyheadersize'] = \strlen($proxydata);

				if (isset($options['sendratelimit']))
					{
					self::ProcessRateLimit($result['rawsendsize'], $result['sendstart'], $options['sendratelimit']);
					}

				if (isset($options['debug_callback']))
					{
					$options['debug_callback']('rawsend', $proxydata, $options['debug_callback_opts']);
					}
				elseif ($debug)
					{
					$result['rawsend'] .= $proxydata;
					}
				// Get the response - success is a 2xx code.
				$options2 = [];

				if (isset($options['recvratelimit']))
					{
					$options2['recvratelimit'] = $options['recvratelimit'];
					}

				if (isset($options['debug_callback']))
					{
					$options2['debug_callback'] = $options['debug_callback'];
					$options2['debug_callback_opts'] = $options['debug_callback_opts'];
					}
				$info = self::GetResponse($fp, $debug, $options2, $startts, $timeout);

				if (! $info['success'])
					{
					\fclose($fp);

					return $info;
					}

				if ('2' != \substr($info['response']['code'], 0, 1))
					{
					\fclose($fp);

					return ['success' => false,
						'error' => self::HTTPTranslate('Expected a 200 response from the CONNECT request.  Received:  %s.', $info['response']['line']),
						'info' => $info,
						'errorcode' => 'proxy_connect_tunnel', ];
					}
				$result['rawrecvsize'] += $info['rawrecvsize'];
				$result['rawrecvheadersize'] += $info['rawrecvheadersize'];

				if (isset($options['debug_callback']))
					{
					$options['debug_callback']('rawrecv', $info['rawrecv'], $options['debug_callback_opts']);
					}
				elseif ($debug)
					{
					$result['rawrecv'] .= $info['rawrecv'];
					}
				}
			}
		else
			{
			if (! isset($options['connecttimeout']))
				{
				$options['connecttimeout'] = 10;
				}
			$timeleft = self::GetTimeLeft($startts, $timeout);

			if (false !== $timeleft)
				{
				$options['connecttimeout'] = \min($options['connecttimeout'], $timeleft);
				}

			if (! \function_exists('stream_socket_client'))
				{
				$fp = @\fsockopen($protocol . '://' . $host, $port, $errornum, $errorstr, $options['connecttimeout']);
				}
			else
				{
				$context = @\stream_context_create();

				if ($secure && isset($options['sslopts']) && \is_array($options['sslopts']) && ('ssl' == $protocol || 'tls' == $protocol))
					{
					self::ProcessSSLOptions($options, 'sslopts', $host);

					foreach ($options['sslopts'] as $key => $val)
						{
						@\stream_context_set_option($context, 'ssl', $key, $val);
						}
					}
				$fp = @\stream_socket_client($protocol . '://' . $host . ':' . $port, $errornum, $errorstr, $options['connecttimeout'], STREAM_CLIENT_CONNECT, $context);
				$contextopts = \stream_context_get_options($context);

				if ($secure && isset($options['sslopts']) && \is_array($options['sslopts']) && ('ssl' == $protocol || 'tls' == $protocol) && isset($contextopts['ssl']['peer_certificate']))
					{
					if (isset($options['debug_callback']))
						{
						$options['debug_callback']('peercert', @\openssl_x509_parse($contextopts['ssl']['peer_certificate']), $options['debug_callback_opts']);
						}
					}
				}

			if (false === $fp)
				{
				return ['success' => false,
					'error' => self::HTTPTranslate("Unable to establish a connection to '%s'.", ($secure ? $protocol . '://' : '') . $host . ':' . $port),
					'info' => $errorstr . ' (' . $errornum . ')',
					'errorcode' => 'connect_failed', ];
				}
			$result['connected'] = \microtime(true);
			}
		// Send the initial data.
		$result['sendstart'] = \microtime(true);
		\fwrite($fp, $data);

		if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
			{
			\fclose($fp);

			return ['success' => false,
				'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
				'errorcode' => 'timeout_exceeded', ];
			}
		$result['rawsendsize'] += \strlen($data);
		$result['rawsendheadersize'] = \strlen($data);

		if (isset($options['sendratelimit']))
			{
			self::ProcessRateLimit($result['rawsendsize'], $result['sendstart'], $options['sendratelimit']);
			}

		if (isset($options['debug_callback']))
			{
			$options['debug_callback']('rawsend', $data, $options['debug_callback_opts']);
			}
		elseif ($debug)
			{
			$result['rawsend'] .= $data;
			}
		// Send extra data.
		if (isset($options['write_body_callback']))
			{
			while ($bodysize > 0)
				{
				$bodysize2 = $bodysize;

				if (! $options['write_body_callback']($body, $bodysize2, $options['write_body_callback_opts']) || \strlen($body) > $bodysize)
					{
					\fclose($fp);

					return ['success' => false,
						'error' => self::HTTPTranslate('HTTP write body callback function failed.'),
						'errorcode' => 'write_body_callback', ];
					}
				\fwrite($fp, $body);

				if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
					{
					\fclose($fp);

					return ['success' => false,
						'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
						'errorcode' => 'timeout_exceeded', ];
					}
				$result['rawsendsize'] += \strlen($body);

				if (isset($options['sendratelimit']))
					{
					self::ProcessRateLimit($result['rawsendsize'], $result['sendstart'], $options['sendratelimit']);
					}

				if (isset($options['debug_callback']))
					{
					$options['debug_callback']('rawsend', $body, $options['debug_callback_opts']);
					}
				elseif ($debug)
					{
					$result['rawsend'] .= $body;
					}
				$bodysize -= \strlen($body);
				$body = '';
				}
			unset($options['write_body_callback'], $options['write_body_callback_opts']);

			}
		elseif (isset($options['files']) && \count($options['files']))
			{
			foreach ($options['files'] as $info)
				{
				$name = self::HeaderValueCleanup($info['name']);
				$name = \str_replace('"', '', $name);
				$filename = self::FilenameSafe(self::ExtractFilename($info['filename']));
				$type = self::HeaderValueCleanup($info['type']);
				$body = '--' . $mime . "\r\n";
				$body .= 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $filename . "\"\r\n";
				$body .= 'Content-Type: ' . $type . "\r\n";
				$body .= "\r\n";
				\fwrite($fp, $body);

				if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
					{
					\fclose($fp);

					return ['success' => false,
						'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
						'errorcode' => 'timeout_exceeded', ];
					}
				$result['rawsendsize'] += \strlen($body);

				if (isset($options['sendratelimit']))
					{
					self::ProcessRateLimit($result['rawsendsize'], $result['sendstart'], $options['sendratelimit']);
					}

				if (isset($options['debug_callback']))
					{
					$options['debug_callback']('rawsend', $body, $options['debug_callback_opts']);
					}
				elseif ($debug)
					{
					$result['rawsend'] .= $body;
					}

				if (isset($info['datafile']))
					{
					$fp2 = @\fopen($info['datafile'], 'rb');

					if (false === $fp2)
						{
						\fclose($fp);

						return ['success' => false,
							'error' => self::HTTPTranslate("The file '%s' does not exist.", $info['datafile']),
							'errorcode' => 'file_open', ];
						}
					// Read/Write 65K at a time.
					while ($info['filesize'] >= 65536)
						{
						$body = \fread($fp2, 65536);

						if (false === $body || 65536 != \strlen($body))
							{
							\fclose($fp2);
							\fclose($fp);

							return ['success' => false,
								'error' => self::HTTPTranslate("A read error was encountered with the file '%s'.", $info['datafile']),
								'errorcode' => 'file_read', ];
							}
						\fwrite($fp, $body);

						if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
							{
							\fclose($fp);

							return ['success' => false,
								'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
								'errorcode' => 'timeout_exceeded', ];
							}
						$result['rawsendsize'] += \strlen($body);

						if (isset($options['sendratelimit']))
							{
							self::ProcessRateLimit($result['rawsendsize'], $result['sendstart'], $options['sendratelimit']);
							}

						if (isset($options['debug_callback']))
							{
							$options['debug_callback']('rawsend', $body, $options['debug_callback_opts']);
							}
						elseif ($debug)
							{
							$result['rawsend'] .= $body;
							}
						$info['filesize'] -= 65536;
						}

					if ($info['filesize'] > 0)
						{
						$body = \fread($fp2, $info['filesize']);

						if (false === $body || \strlen($body) != $info['filesize'])
							{
							\fclose($fp2);
							\fclose($fp);

							return ['success' => false,
								'error' => self::HTTPTranslate("A read error was encountered with the file '%s'.", $info['datafile']),
								'errorcode' => 'file_read', ];
							}
						}
					else
						{
						$body = '';
						}
					\fclose($fp2);
					}
				else
					{
					$body = $info['data'];
					}
				$body .= "\r\n";
				\fwrite($fp, $body);

				if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
					{
					\fclose($fp);

					return ['success' => false,
						'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
						'errorcode' => 'timeout_exceeded', ];
					}
				$result['rawsendsize'] += \strlen($body);

				if (isset($options['sendratelimit']))
					{
					self::ProcessRateLimit($result['rawsendsize'], $result['sendstart'], $options['sendratelimit']);
					}

				if (isset($options['debug_callback']))
					{
					$options['debug_callback']('rawsend', $body, $options['debug_callback_opts']);
					}
				elseif ($debug)
					{
					$result['rawsend'] .= $body;
					}
				}
			$body = '--' . $mime . "--\r\n";
			\fwrite($fp, $body);

			if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
				{
				\fclose($fp);

				return ['success' => false,
					'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
					'errorcode' => 'timeout_exceeded', ];
				}
			$result['rawsendsize'] += \strlen($body);

			if (isset($options['sendratelimit']))
				{
				self::ProcessRateLimit($result['rawsendsize'], $result['sendstart'], $options['sendratelimit']);
				}

			if (isset($options['debug_callback']))
				{
				$options['debug_callback']('rawsend', $body, $options['debug_callback_opts']);
				}
			elseif ($debug)
				{
				$result['rawsend'] .= $body;
				}
			unset($options['files']);
			}
		elseif ($bodysize > 0)
			{
			\fclose($fp);

			return ['success' => false,
				'error' => self::HTTPTranslate('A weird internal HTTP error that should never, ever happen...just happened.'),
				'errorcode' => 'impossible', ];
			}
		// Get the response.
		$info = self::GetResponse($fp, $debug, $options, $startts, $timeout);
		\fclose($fp);
		$info['rawsendsize'] = $result['rawsendsize'];

		if (! $info['success'])
			{
			return $info;
			}
		$result['rawrecvsize'] += $info['rawrecvsize'];
		$result['rawrecvheadersize'] += $info['rawrecvheadersize'];

		if ($debug)
			{
			$result['rawrecv'] .= $info['rawrecv'];
			}
		$result['recvstart'] = $info['recvstart'];
		$result['response'] = $info['response'];
		$result['headers'] = $info['headers'];
		$result['body'] = (string)$info['body'];
		$result['endts'] = \microtime(true);

		return $result;
		}

	public static function HTTPTranslate()
		{
		$args = \func_get_args();

		if (! \count($args))
			{
			return '';
			}

		return \call_user_func_array((\defined('CS_TRANSLATE_FUNC') && \function_exists(CS_TRANSLATE_FUNC) ? CS_TRANSLATE_FUNC : 'sprintf'), $args);
		}

	public static function NormalizeHeaders($headers)
		{
		$result = [];

		foreach ($headers as $name => $val)
			{
			$val = self::HeaderValueCleanup($val);

			if ('' != $val)
				{
				$result[self::HeaderNameCleanup($name)] = $val;
				}
			}

		return $result;
		}

	// Swiped from str_basics.php so this file can be standalone.
	public static function FilenameSafe($filename)
		{
		return \preg_replace('/[_]+/', '_', \preg_replace('/[^A-Za-z0-9_.\-]/', '_', $filename));
		}

	public static function ExtractFilename($dirfile)
		{
		$dirfile = \str_replace('\\', '/', $dirfile);
		$pos = \strrpos($dirfile, '/');

		if (false !== $pos)
			{
			$dirfile = \substr($dirfile, $pos + 1);
			}

		return $dirfile;
		}

	public static function GetTimeLeft($start, $limit)
		{
		if (false === $limit)
			{
			return false;
			}
		$difftime = \microtime(true) - $start;

		if ($difftime >= $limit)
			{
			return 0;
			}

		return $limit - $difftime;
		}

	private static function HeaderValueCleanup($value)
		{
		return \str_replace(["\r",
			"\n", ], ['',
				'', ], $value);
		}

	private static function HeaderNameCleanup($name)
		{
		return \preg_replace('/\s+/', '-', \ucwords(\strtolower(\trim(\preg_replace('/[^A-Za-z0-9 ]/', ' ', $name)))));
		}

	private static function ProcessSSLOptions(&$options, $key, $host) : void
		{
		if (isset($options[$key]['auto_cainfo']))
			{
			unset($options[$key]['auto_cainfo']);
			$cainfo = \ini_get('curl.cainfo');

			if (false !== $cainfo && \strlen($cainfo) > 0)
				{
				$options[$key]['cafile'] = $cainfo;
				}
			elseif (\file_exists(\str_replace('\\', '/', __DIR__) . '/cacert.pem'))
				{
				$options[$key]['cafile'] = \str_replace('\\', '/', __DIR__) . '/cacert.pem';
				}
			}

		if (isset($options[$key]['auto_cn_match']))
			{
			unset($options[$key]['auto_cn_match']);

			if (! isset($options['headers']['Host']))
				{
				$options[$key]['CN_match'] = $host;
				}
			else
				{
				$info = self::ExtractURL('http://' . $options['headers']['Host']);
				$options[$key]['CN_match'] = $info['host'];
				}
			}

		if (isset($options[$key]['auto_sni']))
			{
			unset($options[$key]['auto_sni']);
			$options[$key]['SNI_enabled'] = true;

			if (! isset($options['headers']['Host']))
				{
				$options[$key]['SNI_server_name'] = $host;
				}
			else
				{
				$info = self::ExtractURL('http://' . $options['headers']['Host']);
				$options[$key]['SNI_server_name'] = $info['host'];
				}
			}
		}

	private static function ProcessRateLimit($size, $start, $limit) : void
		{
		$difftime = \microtime(true) - $start;

		if ($difftime > 0.0)
			{
			if ($size / $difftime > $limit)
				{
				// Sleeping for some amount of time will equalize the rate.
				// So, solve this for $x:  $size / ($x + $difftime) = $limit
				\usleep(($size - ($limit * $difftime)) / $limit);
				}
			}
		}

	private static function GetResponse($fp, $debug, $options, $startts, $timeout)
		{
		$recvstart = \microtime(true);
		$rawdata = $data = '';
		$rawsize = $rawrecvheadersize = 0;

		do
			{
			$autodecode = (! isset($options['auto_decode']) || $options['auto_decode']);
			// Process the response line.
			while (false === \strpos($data, "\n") && ($data2 = \fgets($fp, 116000)) !== false)
				{
				if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
					{
					return ['success' => false,
						'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
						'errorcode' => 'timeout_exceeded', ];
					}
				$rawsize += \strlen($data2);
				$data .= $data2;

				if (isset($options['recvratelimit']))
					{
					self::ProcessRateLimit($rawsize, $recvstart, $options['recvratelimit']);
					}

				if (isset($options['debug_callback']))
					{
					$options['debug_callback']('rawrecv', $data2, $options['debug_callback_opts']);
					}
				elseif ($debug)
					{
					$rawdata .= $data2;
					}

				if (\feof($fp))
					{
					break;
					}
				}
			$pos = \strpos($data, "\n");

			if (false === $pos)
				{
				return ['success' => false,
					'error' => self::HTTPTranslate('Unable to retrieve response line.'),
					'errorcode' => 'get_response_line', ];
				}
			$line = \trim(\substr($data, 0, $pos));
			$data = \substr($data, $pos + 1);
			$rawrecvheadersize += $pos + 1;
			$response = \explode(' ', $line, 3);
			$response = ['line' => $line,
				'httpver' => \strtoupper($response[0]),
				'code' => $response[1] ?? '',
				'meaning' => $response[2] ?? '', ];
			// Process the headers.
			$headers = [];
			$lastheader = '';

			do
				{
				while (false === \strpos($data, "\n") && ($data2 = \fgets($fp, 116000)) !== false)
					{
					if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
						{
						return ['success' => false,
							'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
							'errorcode' => 'timeout_exceeded', ];
						}
					$rawsize += \strlen($data2);
					$data .= $data2;

					if (isset($options['recvratelimit']))
						{
						self::ProcessRateLimit($rawsize, $recvstart, $options['recvratelimit']);
						}

					if (isset($options['debug_callback']))
						{
						$options['debug_callback']('rawrecv', $data2, $options['debug_callback_opts']);
						}
					elseif ($debug)
						{
						$rawdata .= $data2;
						}

					if (\feof($fp))
						{
						break;
						}
					}
				$pos = \strpos($data, "\n");

				if (false === $pos)
					{
					$pos = \strlen($data);
					}
				$header = \rtrim(\substr($data, 0, $pos));
				$data = \substr($data, $pos + 1);
				$rawrecvheadersize += $pos + 1;

				if ('' != $header)
					{
					if ('' != $lastheader && ' ' == \substr($header, 0, 1) || "\t" == \substr($header, 0, 1))
						{
						$headers[$lastheader][\count($headers[$lastheader]) - 1] .= $header;
						}
					else
						{
						$pos = \strpos($header, ':');

						if (false === $pos)
							{
							$pos = \strlen($header);
							}
						$lastheader = self::HeaderNameCleanup(\substr($header, 0, $pos));

						if (! isset($headers[$lastheader]))
							{
							$headers[$lastheader] = [];
							}
						$headers[$lastheader][] = \ltrim(\substr($header, $pos + 1));
						}
					}
				}
			while ('' != $header);

			if (100 != $response['code'] && isset($options['read_headers_callback']))
				{
				if (! $options['read_headers_callback']($response, $headers, $options['read_headers_callback_opts']))
					{
					return ['success' => false,
						'error' => self::HTTPTranslate('Read headers callback returned with a failure condition.'),
						'errorcode' => 'read_header_callback', ];
					}
				}
			// Determine if decoding the content is possible and necessary.
			if ($autodecode && ! isset($headers['Content-Encoding']) || ('gzip' != \strtolower($headers['Content-Encoding'][0]) && 'deflate' != \strtolower($headers['Content-Encoding'][0])))
				{
				$autodecode = false;
				}

			if (! $autodecode)
				{
				$autodecode_ds = false;
				}
			else
				{
				// Since servers and browsers do everything wrong, ignore the encoding claim and attempt to auto-detect the encoding.
				$autodecode_ds = new DeflateStream();
				$autodecode_ds->Init('rb', -1, ['type' => 'auto']);
				}
			// Process the body.
			$body = '';

			if (isset($headers['Transfer-Encoding']) && 'chunked' == \strtolower($headers['Transfer-Encoding'][0]))
				{
				do
					{
					// Calculate the next chunked size and ignore chunked extensions.
					while (false === \strpos($data, "\n") && ($data2 = \fgets($fp, 116000)) !== false)
						{
						if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
							{
							return ['success' => false,
								'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
								'errorcode' => 'timeout_exceeded', ];
							}
						$rawsize += \strlen($data2);
						$data .= $data2;

						if (isset($options['recvratelimit']))
							{
							self::ProcessRateLimit($rawsize, $recvstart, $options['recvratelimit']);
							}

						if (isset($options['debug_callback']))
							{
							$options['debug_callback']('rawrecv', $data2, $options['debug_callback_opts']);
							}
						elseif ($debug)
							{
							$rawdata .= $data2;
							}

						if (\feof($fp))
							{
							break;
							}
						}
					$pos = \strpos($data, "\n");

					if (false === $pos)
						{
						$pos = \strlen($data);
						}
					$line = \trim(\substr($data, 0, $pos));
					$data = \substr($data, $pos + 1);
					$pos = \strpos($line, ';');

					if (false === $pos)
						{
						$pos = \strlen($line);
						}
					$size = \hexdec(\substr($line, 0, $pos));

					if ($size < 0)
						{
						$size = 0;
						}
					// Retrieve content.
					$size2 = $size;
					$size3 = \min(\strlen($data), $size);

					if ($size3 > 0)
						{
						$data2 = \substr($data, 0, $size3);
						$data = \substr($data, $size3);
						$size2 -= $size3;

						if (100 == $response['code'] || ! isset($options['read_body_callback']))
							{
							$body .= self::GetDecodedBody($autodecode_ds, $data2);
							}
						elseif (! $options['read_body_callback']($response, self::GetDecodedBody($autodecode_ds, $data2), $options['read_body_callback_opts']))
							{
							return ['success' => false,
								'error' => self::HTTPTranslate('Read body callback returned with a failure condition.'),
								'errorcode' => 'read_body_callback', ];
							}
						}

					while ($size2 > 0 && ($data2 = \fread($fp, ($size2 > 65536 ? 65536 : $size2))) !== false)
						{
						if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
							{
							return ['success' => false,
								'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
								'errorcode' => 'timeout_exceeded', ];
							}
						$tempsize = \strlen($data2);
						$rawsize += $tempsize;
						$size2 -= $tempsize;

						if (100 == $response['code'] || ! isset($options['read_body_callback']))
							{
							$body .= self::GetDecodedBody($autodecode_ds, $data2);
							}
						elseif (! $options['read_body_callback']($response, self::GetDecodedBody($autodecode_ds, $data2), $options['read_body_callback_opts']))
							{
							return ['success' => false,
								'error' => self::HTTPTranslate('Read body callback returned with a failure condition.'),
								'errorcode' => 'read_body_callback', ];
							}

						if (isset($options['recvratelimit']))
							{
							self::ProcessRateLimit($rawsize, $recvstart, $options['recvratelimit']);
							}

						if (isset($options['debug_callback']))
							{
							$options['debug_callback']('rawrecv', $data2, $options['debug_callback_opts']);
							}
						elseif ($debug)
							{
							$rawdata .= $data2;
							}

						if (\feof($fp))
							{
							break;
							}
						}
					// Ignore one newline.
					while (false === \strpos($data, "\n") && ($data2 = \fgets($fp, 116000)) !== false)
						{
						if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
							{
							return ['success' => false,
								'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
								'errorcode' => 'timeout_exceeded', ];
							}
						$rawsize += \strlen($data2);
						$data .= $data2;

						if (isset($options['recvratelimit']))
							{
							self::ProcessRateLimit($rawsize, $recvstart, $options['recvratelimit']);
							}

						if (isset($options['debug_callback']))
							{
							$options['debug_callback']('rawrecv', $data2, $options['debug_callback_opts']);
							}
						elseif ($debug)
							{
							$rawdata .= $data2;
							}

						if (\feof($fp))
							{
							break;
							}
						}
					$pos = \strpos($data, "\n");

					if (false === $pos)
						{
						$pos = \strlen($data);
						}
					$data = \substr($data, $pos + 1);
					}
				while ($size);
				// Process additional headers.
				$lastheader = '';

				do
					{
					while (false === \strpos($data, "\n") && ($data2 = \fgets($fp, 116000)) !== false)
						{
						if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
							{
							return ['success' => false,
								'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
								'errorcode' => 'timeout_exceeded', ];
							}
						$rawsize += \strlen($data2);
						$data .= $data2;

						if (isset($options['recvratelimit']))
							{
							self::ProcessRateLimit($rawsize, $recvstart, $options['recvratelimit']);
							}

						if (isset($options['debug_callback']))
							{
							$options['debug_callback']('rawrecv', $data2, $options['debug_callback_opts']);
							}
						elseif ($debug)
							{
							$rawdata .= $data2;
							}

						if (\feof($fp))
							{
							break;
							}
						}
					$pos = \strpos($data, "\n");

					if (false === $pos)
						{
						$pos = \strlen($data);
						}
					$header = \rtrim(\substr($data, 0, $pos));
					$data = \substr($data, $pos + 1);
					$rawrecvheadersize += $pos + 1;

					if ('' != $header)
						{
						if ('' != $lastheader && (' ' == \substr($header, 0, 1) || "\t" == \substr($header, 0, 1)))
							{
							$headers[$lastheader][\count($headers[$lastheader]) - 1] .= $header;
							}
						else
							{
							$pos = \strpos($header, ':');

							if (false === $pos)
								{
								$pos = \strlen($header);
								}
							$lastheader = self::HeaderNameCleanup(\substr($header, 0, $pos));

							if (! isset($headers[$lastheader]))
								{
								$headers[$lastheader] = [];
								}
							$headers[$lastheader][] = \ltrim(\substr($header, $pos + 1));
							}
						}
					}
				while ('' != $header);

				if (100 != $response['code'] && isset($options['read_headers_callback']))
					{
					if (! $options['read_headers_callback']($response, $headers, $options['read_headers_callback_opts']))
						{
						return ['success' => false,
							'error' => self::HTTPTranslate('Read headers callback returned with a failure condition.'),
							'errorcode' => 'read_header_callback', ];
						}
					}
				}
			elseif (isset($headers['Content-Length']))
				{
				$size = (int)$headers['Content-Length'][0];
				$datasize = 0;

				while ($datasize < $size && ($data2 = \fread($fp, ($size > 65536 ? 65536 : $size))) !== false)
					{
					if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
						{
						return ['success' => false,
							'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
							'errorcode' => 'timeout_exceeded', ];
						}
					$tempsize = \strlen($data2);
					$datasize += $tempsize;
					$rawsize += $tempsize;

					if (100 == $response['code'] || ! isset($options['read_body_callback']))
						{
						$body .= self::GetDecodedBody($autodecode_ds, $data2);
						}
					elseif (! $options['read_body_callback']($response, self::GetDecodedBody($autodecode_ds, $data2), $options['read_body_callback_opts']))
						{
						return ['success' => false,
							'error' => self::HTTPTranslate('Read body callback returned with a failure condition.'),
							'errorcode' => 'read_body_callback', ];
						}

					if (isset($options['recvratelimit']))
						{
						self::ProcessRateLimit($rawsize, $recvstart, $options['recvratelimit']);
						}

					if (isset($options['debug_callback']))
						{
						$options['debug_callback']('rawrecv', $data2, $options['debug_callback_opts']);
						}
					elseif ($debug)
						{
						$rawdata .= $data2;
						}

					if (\feof($fp))
						{
						break;
						}
					}
				}
			elseif (100 != $response['code'])
				{
				while (($data2 = \fread($fp, 65536)) !== false)
					{
					if (false !== $timeout && 0 == self::GetTimeLeft($startts, $timeout))
						{
						return ['success' => false,
							'error' => self::HTTPTranslate('HTTP timeout exceeded.'),
							'errorcode' => 'timeout_exceeded', ];
						}
					$tempsize = \strlen($data2);
					$rawsize += $tempsize;

					if (100 == $response['code'] || ! isset($options['read_body_callback']))
						{
						$body .= self::GetDecodedBody($autodecode_ds, $data2);
						}
					elseif (! $options['read_body_callback']($response, self::GetDecodedBody($autodecode_ds, $data2), $options['read_body_callback_opts']))
						{
						return ['success' => false,
							'error' => self::HTTPTranslate('Read body callback returned with a failure condition.'),
							'errorcode' => 'read_body_callback', ];
						}

					if (isset($options['recvratelimit']))
						{
						self::ProcessRateLimit($rawsize, $recvstart, $options['recvratelimit']);
						}

					if (isset($options['debug_callback']))
						{
						$options['debug_callback']('rawrecv', $data2, $options['debug_callback_opts']);
						}
					elseif ($debug)
						{
						$rawdata .= $data2;
						}

					if (\feof($fp))
						{
						break;
						}
					}
				}

			if (false !== $autodecode_ds)
				{
				$autodecode_ds->Finalize();
				$data2 = $autodecode_ds->Read();

				if (100 == $response['code'] || ! isset($options['read_body_callback']))
					{
					$body .= $data2;
					}
				elseif (! $options['read_body_callback']($response, $data2, $options['read_body_callback_opts']))
					{
					return ['success' => false,
						'error' => self::HTTPTranslate('Read body callback returned with a failure condition.'),
						'errorcode' => 'read_body_callback', ];
					}
				}
			}
		while (100 == $response['code']);

		return ['success' => true,
			'rawrecv' => $rawdata,
			'rawrecvsize' => $rawsize,
			'rawrecvheadersize' => $rawrecvheadersize,
			'recvstart' => $recvstart,
			'response' => $response,
			'headers' => $headers,
			'body' => $body, ];
		}

	private static function GetDecodedBody(&$autodecode_ds, $body)
		{
		if (false !== $autodecode_ds)
			{
			$autodecode_ds->Write($body);
			$body = $autodecode_ds->Read();
			}

		return $body;
		}
	}
