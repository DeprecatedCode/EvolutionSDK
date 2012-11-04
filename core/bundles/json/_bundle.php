<?php

/**
 * EvolutionSDK JSON Bundle
 *
 * @author Nate Ferrero
 */
namespace e\Json;
use Exception;
use e;

class Bundle {

	public function encode($in, $indent = 0) {

		$_escape = function ($str) {
			return preg_replace("!([\b\t\n\r\f\"\\])!", "\\\\\\1", $str);
		};

		$out = '';

		foreach ($in as $key => $value) {
			$out .= str_repeat("\t", $indent + 1);
			$out .= "\"".$_escape((string)$key)."\": ";

			if (is_object($value) || is_array($value)) {
				$out .= "\n";
				$out .= $this->encode($value, $indent + 1);
			}
			elseif (is_bool($value)) {
				$out .= $value ? 'true' : 'false';
			}
			elseif (is_null($value)) {
				$out .= 'null';
			}
			elseif (is_string($value)) {
				$out .= "\"" . $_escape($value) ."\"";
			}
			else {
				$out .= $value;
			}

			$out .= ",\n";
		}

		if (!empty($out)) {
			$out = substr($out, 0, -2);
		}

		$out = str_repeat("\t", $indent) . "{\n" . $out;
		$out .= "\n" . str_repeat("\t", $indent) . "}";

		return $out;
	}

}
