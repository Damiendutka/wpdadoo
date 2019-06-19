<?php
/**
 * The file that defines the localisation functionnalities
 *
 * @link       http://www.6tem9.com
 * @since      1.0.0
 *
 * @package    6tem9Fonction
 * @subpackage 6tem9Fonction/inc/class
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    6tem9Fonction
 * @subpackage 6tem9Fonction/inc/class
 * @author     6tem9 <contact@6tem9.com>
 */
class localisation {

    /**
     * wpdb of WordPress core.
     *
     * @var object
     */
    public $wpdb;

    /**
     * number results of request.
     *
     * @var int
     */
    public $numRows = 0;

	/**
	 * Initialize 
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
	}

    /**
    * Retourne sous forme d'un tableau la liste des départements français
    *
    * @since    1.0.0
    */
	public function getDepartmentFrance()
	{
		$tabDepartment = $this->wpdb->get_results( "SELECT Dep, Nom, Id FROM {$this->wpdb->prefix}departementsfrance", OBJECT_K );
		return $tabDepartment;
	}

}