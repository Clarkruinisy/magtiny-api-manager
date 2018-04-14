<?php

/**
 * Magtiny API manager default config parameters. Please do not modify data here.
 * If you want to modify manager config parameters. Please pass the array parameters
 * when you instance the apiManager just as following:
 * new \magtiny\tool\apiManager($config)
 * The "instanceDir", "secret" and "instanceUrl" are required.
 * The "parseFields", "messages" parameter should not be modified or override.
 */

return [
	"secret"		=> "",
	"instanceDir"	=> "",
	"instanceUrl"	=> "",
	"document"		=> "",
	"sessionUse"	=> "cookie",
	"sessionKey"	=> "PHPSESSID",
	"fileExtension"	=> "php",
	"markField"		=> "magtiny",
	"parseFields"	=> ["controller", "action", "method", "label", "param", "file", "ctime"],
	"methodValues"	=> ["get", "post", "put", "delete", "options"],
	"defaultMethod"	=> "post",
	"labelValues"	=> ["login", "logout", "access", "allowed"],
	"defaultLabel"	=> "access",
	"messages"		=> [
		1000 => "Magtiny API manager default response massage",
		1001 => "Magtiny connect secret is required",
		1002 => "Magtiny connect secret is incorrect.",
		1003 => "Magtiny manager request parameter value is incorrect.",
		1004 => "Magtiny instance dir is required",
		1005 => "Magtiny instance dir is not exist.",
		1006 => "Magtiny instance request url is required.",
		1007 => "Lack of access to magtiny instance dir.",
		1008 => "Magtiny is lack of access to the controller files.",
		1009 => "Connect to magtiny instance success.",
		1010 => "Retrieve magtiny instance data success.",
		1011 => "Retrieve magtiny instance document success.",
		1012 => "Magtiny comment format is incorrect.",
		1013 => "Magtiny comment field is incrrect.",
		1014 => "Magtiny controller is lack of comment",
	]
];

