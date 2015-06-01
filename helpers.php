<?php

class NmoHelpers 
{
	/**
	 * Strip query string from URL
	 * 
	 * @param  string $path URL
	 * @return string       Sanitised URL
	 */
	public static function strip_query($path) {
	  $pos = strpos($path, '?');
	  if ($pos !== false) {
	    $path = substr($path, 0, $pos);
	  }

	  return $path;
	}
}
