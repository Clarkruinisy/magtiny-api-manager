<?php

namespace magtiny\tool;

use magtiny\framework\globals;
use magtiny\framework\render;


class apiManager
{
	private $config = [];

	public function __construct ($config = [])
	{
		$this->config = $config;
	}

	public function start ()
	{
		$method = globals::get("request");
		if (!method_exists($this, $method)) {
			return render::api(1001);
		}
		return $this->{$method}();
	}

	public function connect ()
	{
		if (globals::get("secret") !== $this->config["secret"]) {
			return render::api(1002);
		}
		return render::api(1003, true);
	}

	public function parseAPI ()
	{
		$connect = $this->connect();
		if (!$connect["success"]) {
			return $connect;
		}
		$files = scandir($this->config["dir"]);
		$servers = [];
		$k = -1;
		foreach ($files as $file) {
			if ("." !== $file and ".." !== $file and $controller = lcfirst(strstr($file, ".php", true))){
				++$k;
				$handler = fopen($this->config["dir"]."/".$file, "r");
				if (false === $handler) {
					return render::api(1004);
				}
				$servers[$k] = [
					"file" => $file,
					"controller" => $controller,
					"actions" => []
				];
				$j = -1;
				while (false !== ($content = fgets($handler))) {
					if (0 === strpos(trim($content), '/**')) {
						while (false !== ($innerContent = fgets($handler))) {
							if (0 === strpos(trim($innerContent), '**/')) {
								++$j;
								break;
							}
							if (strpos($innerContent, "@controller")) {
								$servers[$k]["controllerName"] = trim(explode("@controller", $innerContent)[1]);
							}
							if (strpos($innerContent, "@path")) {
								$servers[$k]["controllerPath"] = trim(explode("@path", $innerContent)[1]);
							}
							if (strpos($innerContent, "@action")) {
								$servers[$k]["actions"][$j]["actionName"] = trim(explode("@action", $innerContent)[1]);
							}
							if (strpos($innerContent, "@name")) {
								$servers[$k]["actions"][$j]["actionPath"] = trim(explode("@name", $innerContent)[1]);
							}
							if (strpos($innerContent, "@method")) {
								$servers[$k]["actions"][$j]["method"] = trim(explode("@method", $innerContent)[1]);
							}
							if (strpos($innerContent, "@label")) {
								$servers[$k]["actions"][$j]["label"] = trim(explode("@label", $innerContent)[1]);
							}
							if (strpos($innerContent, "@param")) {
								$paramInfo = preg_split("/[\s]+/", trim(explode("@param", $innerContent)[1]));
								if (0 === strpos($paramInfo[0], "*")) {
									$key = substr($paramInfo[0], 1);
									$require = true;
								}else{
									$key = $paramInfo[0];
									$require = false;
								}
								$servers[$k]["actions"][$j]["param"][] = [
									"key" => $key,
									"type" => "param",
									"require" => $require,
									"default" => $paramInfo[1],
									"method" => isset($paramInfo[2]) ? $paramInfo[2] : $servers[$k]["actions"][$j]["method"]
								];
							}
							if (strpos($innerContent, "@file")) {
								$paramInfo = preg_split("/[\s]+/", trim(explode("@file", $innerContent)[1]));
								if (0 === strpos($paramInfo[0], "*")) {
									$key = substr($paramInfo[0], 1);
									$require = true;
								}else{
									$key = $paramInfo[0];
									$require = false;
								}
								$servers[$k]["actions"][$j]["param"][] = [
									"key" => $key,
									"type" => "file",
									"require" => $require,
									"default" => isset($paramInfo[1]) ? $paramInfo[1] : ""
								];
							}
						}
					}
				}
			}
		}
		$data = [
			"servers" => $servers,
			"url" => $this->config["url"],
			"codeMap" => $this->config["codeMap"],
			"sessionUse" => $this->config["sessionUse"],
			"sessionKey" => $this->config["sessionKey"],
		];
		return render::api(1005, true, $data);
	}
}
