<?php if (!defined("BASEPATH")) exit("No direct script access allowed");
/**
 * 자바스크립트 관련 helper 모음.
 * @author jbKim
 * @since version 1.0
 */
class Js
{

	function tinyjs($content)
	{
		return "
		<meta charset=\"utf-8\">\n
		<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n
		<script type=\"text/javascript\" charset=\"utf-8\">" . $content . "</script>
		";
	}

	function alert($msg)
	{
		$msg = str_replace("\n", "", $msg);
		echo $this->tinyjs("alert(\"" . $msg . "\");\n");
	}

	function pageRedirect($url, $msg = "", $target = "self")
	{
		if ($msg) {
			$this->alert($msg);
		}
		echo $this->tinyjs($target . ".location.replace(\"" . $url . "\")");
	}

	function pageLocation($url, $msg = "", $target = "self")
	{
		if ($msg) {
			$this->alert($msg);
		}
		echo $this->tinyjs($target . ".location.href=\"$url\"");
	}

	function pageBack($msg = "")
	{
		if ($msg) {
			$this->alert($msg);
		}
		echo $this->tinyjs("history.back();");
	}

	function pageReload($msg = "", $target = "self")
	{
		if ($msg) {
			$this->alert($msg);
		}
		echo $this->tinyjs($target . ".location.reload();");
	}

	function pageClose($msg = "")
	{
		if ($msg) {
			$this->alert($msg);
		}
		echo $this->tinyjs("self.close();");
	}

	function openerRedirect($url, $msg = "")
	{
		if ($msg) {
			$this->alert($msg);
		}
		echo $this->tinyjs("opener.location.replace(\"" . $url, "\")");
	}
}
