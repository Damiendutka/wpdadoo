<?php
/**
 * The file that defines the class DateTime french
 *
 * @link       http://www.6tem9.com
 * @since      1.0.0
 *
 * @package    lld-fonctions
 * @subpackage lld-fonctions/inc/class
 */

/**
 * French translation of DateTime
 *
 * @package    lld-fonctions
 * @subpackage lld-fonctions/inc/class
 * @author     La Luciole Digitlae <contact@lalucioledigitale.com>
 */
if ( !class_exists('DateTimeFrench') )
{
	class DateTimeFrench extends DateTime 
	{
	 
	    /**
	     * Active language
	     *
	     * @var string
	     */
	    public $locale = 'fr';

	    /**
	    * Return french translation
	    *
	    * @since    1.0.0
	    */	    
	    public function format( $format ) 
	    {
	        if ( $this->locale == 'fr' )
	        {
		        $daysTranlsate = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
		        $daysShortTranlsate = array('lun', 'mar', 'mer', 'jeu', 'ven', 'sam', 'dim');
	        	$monthsTranslate = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
	        	$monthsShortTranslate = array('janv', 'fév', 'mars', 'avr', 'mai', 'juin', 'juillet', 'août', 'sept', 'oct', 'nov', 'déc');
		    }
			
			$daysEnglish = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
			$daysShortEnglish = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
	        $monthsEnglish = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	        $monthsShortEnglish = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec');

	        return str_replace( $monthsShortEnglish, $monthsShortTranslate, str_replace( $daysShortEnglish, $daysShortTranlsate, str_replace( $monthsEnglish, $monthsTranslate, str_replace($daysEnglish, $daysTranlsate, parent::format($format)) ) ) );
	    }
	}

}