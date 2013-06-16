<?php
	function get_fortune() { 
		$lines = file('fortune.txt') ; 
		return sprintf($lines[array_rand($lines)], count($lines)); 
	}
?>