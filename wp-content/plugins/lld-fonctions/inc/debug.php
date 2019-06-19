<?php
# -------------------------------------------------------------------------------------------------------------------------------
#  Affichage plus graphique de print_r
if ( !function_exists('n_print') )
{
	function n_print($data, $display = true, $name = '') 
	{
		$aBackTrace = debug_backtrace();
		$theDisplay = '<h4>'. $name . '</h4>';
		$theDisplay .= '<fieldset style="border: 1px solid orange; padding: 5px;color: #333; background-color: #fff;">';
		$theDisplay .= '<legend style="border:1px solid orange;padding: 1px;background-color:#eee;color:orange;">' . basename($aBackTrace[0]['file']) . ' ligne => ' . $aBackTrace[0]['line'] . '</legend>';
		$theDisplay .= '<pre>'. htmlentities(print_r($data, 1)) . '</pre>';
		$theDisplay .= '</fieldset><br />';

		if ( $display )
			echo $theDisplay;
		else
			return $theDisplay;
	}
}

?>