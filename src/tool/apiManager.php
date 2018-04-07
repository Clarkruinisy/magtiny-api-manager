<?php

namespace magtiny\tool;

use magtiny\framework\globals;
use magtiny\framework\render;


class apiManager
{
	private $config = [];

	public function __construct ($config = [])
	{
		$defaultConfig = render::config(__DIR__."/../config.php");
		$this->config = array_merge($defaultConfig, $config);
	}

	private function render ($code = 1000, $success = false, $data = null)
	{
		return [
			"success" => $success,
			"code" => $code,
			"message" => $this->config["messages"][$code],
			"data" => $data,
		];
	}

	public function start ()
	{
		if (!globals::input("secret")) {
			return $this->render(1001);
		}
		if (globals::input("secret") !== $this->config["secret"]) {
			return $this->render(1002);
		}
		$method = globals::input("request");
		if (!method_exists($this, $method)) {
			return $this->render(1003);
		}
		return call_user_func_array([$this, $method], []);
	}

	public function connect ()
	{
		return $this->render(1009, true);
	}

	public function manager ()
	{
		if (!$this->config["instanceDir"]) {
			return $this->render(1004);
		}
		if (!is_dir($this->config["instanceDir"])) {
			return $this->render(1005);
		}
		if (!$this->config["instanceUrl"]) {
			return $this->render(1006);
		}
		$files = scandir($this->config["instanceDir"]);
		if (false === $files) {
			return $this->render(1007);
		}
		$servers = [];
		$k = -1;
		foreach ($files as $file) {
			if ("." !== $file and ".." !== $file and $controller = lcfirst(strstr($file, ".php", true))){
				++$k;
				if (!is_readable($this->config["instanceDir"]."/".$file)) {
					return $this->render(1008);
				}
				$handler = fopen($this->config["instanceDir"]."/".$file, "r");
				$servers[$k] = [
					"file" => $this->config["instanceDir"]."/".$file,
					"ctime" => filectime($this->config["instanceDir"]."/".$file),
					"controller" => $controller,
					"controllerName" => $controller,
					"actions" => []
				];
				$j = -1;
				$lineNumber = 0;
				while (false !== ($content = fgets($handler, 4096))) {
					++ $lineNumber;
					if (0 === strpos(trim($content), "/**")) {
						$innerContent = fgets($handler, 4096);
						++ $lineNumber;
						if (strpos(trim($innerContent), "@magtiny")) {
							$contentArray = explode("@magtiny", $innerContent);
							if (2 !== count($contentArray)) {
								$data = $servers[$k]["file"]."(".$lineNumber.")";
								return $this->render(1012, false, $data);
							}
							$value = trim($contentArray[1]);
							if (!$value) {
								$data = $servers[$k]["file"]."(".$lineNumber.")";
								return $this->render(1012, false, $data);
							}
							if (-1 === $j) {
								$servers[$k]["controllerName"] = $value;
							}else{
								$servers[$k]["actions"][$j]["actionName"] = $value;
							}
							while (false !== ($innerContent = fgets($handler, 4096))) {
								++ $lineNumber;
								if (0 === strpos(trim($innerContent), "**/")) {
									if (-1 === $j and isset($servers[$k]["actions"][$j])) {
										$data = $servers[$k]["file"];
										return $this->render(1014, false, $data);
									}
									if (-1 === $j or isset($servers[$k]["actions"][$j])) {
										++ $j;
									}
									break;
								}
								foreach ($this->config["parseFields"] as $field) {
									if (strpos($innerContent, "@".$field)) {
										$contentArray = explode("@".$field, $innerContent);
										if (2 !== count($contentArray) and "file" !== $field) {
											$data = $servers[$k]["file"]."(".$lineNumber.")";
											return $this->render(1012, false, $data);
										}
										$value = trim($contentArray[1]);
										if (!$value) {
											$data = $servers[$k]["file"]."(".$lineNumber.")";
											return $this->render(1012, false, $data);
										}
										switch ($field) {
											case "controller":
												$servers[$k][$field] = $value;
												break;
											case "action":
											case "method":
											case "label":
												$servers[$k]["actions"][$j][$field] = $value;
												break;
											case "param":
											case "file":
												preg_match("/[\s]+/", $value, $matches, PREG_OFFSET_CAPTURE);
												$param = ["type" => $field];
												if (isset($matches[0][1])) {
													$sepPos = $matches[0][1];
													$param["default"] = trim(substr($value, $sepPos));
													$paramKeyInfo = substr($value, 0, $sepPos);
												}else{
													$param["default"] = "";
													$paramKeyInfo = $value;
												}
												
												if (strpos($paramKeyInfo, ".")) {
													$paramArray = explode(".", $paramKeyInfo);
													$param["key"] = current($paramArray);
													while (false !== ($next = next($paramArray))) {
														switch ($next) {
															case "*":
																$param["required"] = true;
																break;
															case "get":
															case "json":
																if ("file" === $field) {
																	$data = $servers[$k]["file"]."(".$lineNumber.")";
																	return $this->render(1012, false, $data);
																}
																$param["method"] = $next;
																break;
															default:
																$data = $servers[$k]["file"]."(".$lineNumber.")";
																return $this->render(1012, false, $data);
														}
													}
												}else{
													$param["key"] = $paramKeyInfo;
													$param["required"] = false;
												}
												$servers[$k]["actions"][$j]["param"][] = $param;
												break;
											default:
												return $this->render(1013);
										}
										break;
									}
								}
							}
						}
					}
				}
			}
		}
		$data = [
			"servers" => $servers,
			"time" => time(),
			"instanceUrl" => $this->config["instanceUrl"],
			"sessionUse" => $this->config["sessionUse"],
			"sessionKey" => $this->config["sessionKey"],
		];
		return $this->render(1010, true, $data);
	}

	public function document ()
	{
		$data = [
			"document" => $this->config["document"],
		];
		return $this->render(1011, true, $data);
	}
}

