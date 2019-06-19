<?php
/*
	Liste des fonctions présentes : 
	- fOptionListeCategorie
	- fTableauLanguePolylang
	- fTableCountries
	- fPluginIsActivate
	- fTableauPays
	- fUrlCourante
	- fReditectionPageProtege
	- fArticlePlusLu
	- fArticlePlusCommente
	- fListeNombre
	- fListeAnnee
	- fListeMois
	- fEmbedHtmlFront
	- fFermetureBaliseHtml
	- fRemoveMoreTag
	- fDebutTexte
	- fExtraitPost
	- fSuppressionAccent
	- fNetoyageApostrophe
	- fPagination
	- fSupprimerEditeurTheme
	- fUpdateFichier
	- fDeclarationVariableGlobaleJS
	- fSEODefaultOption
	- fSEOInitDefaultOption
	- fGetMenuIdByName
	- fRedirectLostPasswordUrl
	- fRedirectNetworkSiteUrlLostPassword
	- fRetrievePasswordMessageRedirect
	- fRemoveAllHelpTabs
	- fSearchBarMySitesStyleScript
	- fGetAttachmentIdByUrl
	- fTypeContenuRSS
	- fEclosureImageRSS
	- fAjoutVignetteRSS
	- fAfficherInfosPost
	- fGravityFormsLicenceKey
	- fFlushRules
	- fRemoveNodeMySitesIfOneSite
	- fRemoveMenuItemMySitesIfOneSite
	- fIpClient
	- fMimeContentType
	- fRemoveNotAdminSite
	- fChaineFormatee
	- fGetClientIpServer
	- fListBotSearchEngine
*/

function get_coordonees_from_adresse( $address )
{
        // echo 'https://maps.googleapis.com/maps/api/geocode/json?address=' . rawurlencode($address);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address=' . rawurlencode($address) . '&key=AIzaSyDBJ8-fVvHk8C0ZuOSG8L9QLIr5LMp36ps');
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    $json = curl_exec($curl);
    // n_print($json, 'json');

    curl_close ($curl);
    $tabJson = json_decode( $json, true );

    // echo '<br>lat = ' . $tabJson['results'][0]['geometry']['location']['lat'];
    // echo '<br>status = ' . $tabJson['status'];
    // n_print($tabJson);


    if ( $tabJson['status'] == 'OK' )
    {
        $tabgeo['lat'] = $tabJson['results'][0]['geometry']['location']['lat'];
        $tabgeo['lng'] = $tabJson['results'][0]['geometry']['location']['lng'];
        $tabgeo['formatted_address'] = $tabJson['results'][0]['formatted_address'];
    }
    else
    {
        $tabgeo['lat'] = '';
        $tabgeo['lng'] = '';
        $tabgeo['tabJson'] = $tabJson;
    }
    

    // print_r( $tabgeo, 'geo' );
    return $tabgeo;
} 

/**
* Random string
*
* @since    1.5.6
*/
function fGenerateRandomString( $length = 15 ) 
{
    return substr(sha1(rand()), 0, $length);
}

/**
* Retourne l'adresse IP du client
*
* @since    1.5.6
*/
function fGetClientIpServer() 
{
	$ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}

/**
* Permet d'obtenir un tableau permettant d'exploiter le hiérarchisation des terms
*
* @since    1.5.6
* @param 	int 	$tabTerms 	Tableau contenant un tableau d'objet terms
* @param 	int 	$termParent 	Id du terms parent
* @param 	string 	$prefix 	Valeur du préfix propoger
* @param 	string 	$iterationPrefix 	Valeur à rajouter à chaque itération du préfixe
* OR
* @param 	array 	$arguments 	On récupére l'argument d'index 0 si il s'agit d'un tableau de paramètres
*/
function fOptionListeCategorie( $tabTerms, $termParent=0, $prefix=true, $iterationPrefix='---' ) 
{
	global $post;
	$return = array();
	$arguments = func_get_args();
	if ( sizeof($arguments)==1 && is_array($arguments[0]) )
	{
		// Un tableau est fournis en parametre
		// Tableau par defaut
		$tabDefautArgument = array(
			'tabTerms' => array(),
			'termParent' => 0,
			'iterationPrefix' => '',
			'prefix' => true,
		);
		
		// Fusionne le tableau des variables passe en argument avec le tableau des variables par defaut
		$tabArgument = array_replace( $tabDefautArgument, $arguments[0] );

		$tabTerms = $tabArgument['tabTerms'];
		$termParent = $tabArgument['termParent'];
		$prefix = $tabArgument['prefix'];
		$iterationPrefix = $tabArgument['iterationPrefix'];
	}
	else
	{
		$tabTerms = isset($arguments[0]) ? $arguments[0] : '';
		$termParent = isset($arguments[1]) ? $arguments[1] : 0;
		$prefix = isset($arguments[2]) ? $arguments[2] : 0;
		$iterationPrefix = isset($arguments[3]) ? $arguments[3] : 0;
	}

	foreach ( $tabTerms as $key => $term ) 
	{
		if ( $term->parent == $termParent ) 
		{
			$return[$term->term_id]['parent'] = $termParent;
			$return[$term->term_id]['name'] = $prefix=='' ? $term->name : $prefix. '&nbsp;' . $term->name;
			$return[$term->term_id]['nombreItem'] = ' (' . $term->count . ')';
			$return[$term->term_id]['href'] = get_site_url() . '/category-' . $post->post_type . '/' . $term->slug;
			$return[$term->term_id]['termLink'] = get_term_link( $term );
			$return[$term->term_id]['slug'] = $term->slug;
			$return[$term->term_id]['termId'] = $term->term_id;
			$return = array_merge( $return, fOptionListeCategorie( $tabTerms, $term->term_id, $prefix.$iterationPrefix ) );
		}
	}
	return $return;
}

# -----------------------------------------------------------------------------------------------------
function fTableCountries()
{
	return array(
		'AF' => __( 'Afghanistan', '6tem9Countries' ),
		'AX' => __( '&#197;land Islands', '6tem9Countries' ),
		'AL' => __( 'Albania', '6tem9Countries' ),
		'DZ' => __( 'Algeria', '6tem9Countries' ),
		'AD' => __( 'Andorra', '6tem9Countries' ),
		'AO' => __( 'Angola', '6tem9Countries' ),
		'AI' => __( 'Anguilla', '6tem9Countries' ),
		'AQ' => __( 'Antarctica', '6tem9Countries' ),
		'AG' => __( 'Antigua and Barbuda', '6tem9Countries' ),
		'AR' => __( 'Argentina', '6tem9Countries' ),
		'AM' => __( 'Armenia', '6tem9Countries' ),
		'AW' => __( 'Aruba', '6tem9Countries' ),
		'AU' => __( 'Australia', '6tem9Countries' ),
		'AT' => __( 'Austria', '6tem9Countries' ),
		'AZ' => __( 'Azerbaijan', '6tem9Countries' ),
		'BS' => __( 'Bahamas', '6tem9Countries' ),
		'BH' => __( 'Bahrain', '6tem9Countries' ),
		'BD' => __( 'Bangladesh', '6tem9Countries' ),
		'BB' => __( 'Barbados', '6tem9Countries' ),
		'BY' => __( 'Belarus', '6tem9Countries' ),
		'BE' => __( 'Belgium', '6tem9Countries' ),
		'PW' => __( 'Belau', '6tem9Countries' ),
		'BZ' => __( 'Belize', '6tem9Countries' ),
		'BJ' => __( 'Benin', '6tem9Countries' ),
		'BM' => __( 'Bermuda', '6tem9Countries' ),
		'BT' => __( 'Bhutan', '6tem9Countries' ),
		'BO' => __( 'Bolivia', '6tem9Countries' ),
		'BQ' => __( 'Bonaire, Saint Eustatius and Saba', '6tem9Countries' ),
		'BA' => __( 'Bosnia and Herzegovina', '6tem9Countries' ),
		'BW' => __( 'Botswana', '6tem9Countries' ),
		'BV' => __( 'Bouvet Island', '6tem9Countries' ),
		'BR' => __( 'Brazil', '6tem9Countries' ),
		'IO' => __( 'British Indian Ocean Territory', '6tem9Countries' ),
		'VG' => __( 'British Virgin Islands', '6tem9Countries' ),
		'BN' => __( 'Brunei', '6tem9Countries' ),
		'BG' => __( 'Bulgaria', '6tem9Countries' ),
		'BF' => __( 'Burkina Faso', '6tem9Countries' ),
		'BI' => __( 'Burundi', '6tem9Countries' ),
		'KH' => __( 'Cambodia', '6tem9Countries' ),
		'CM' => __( 'Cameroon', '6tem9Countries' ),
		'CA' => __( 'Canada', '6tem9Countries' ),
		'CV' => __( 'Cape Verde', '6tem9Countries' ),
		'KY' => __( 'Cayman Islands', '6tem9Countries' ),
		'CF' => __( 'Central African Republic', '6tem9Countries' ),
		'TD' => __( 'Chad', '6tem9Countries' ),
		'CL' => __( 'Chile', '6tem9Countries' ),
		'CN' => __( 'China', '6tem9Countries' ),
		'CX' => __( 'Christmas Island', '6tem9Countries' ),
		'CC' => __( 'Cocos (Keeling) Islands', '6tem9Countries' ),
		'CO' => __( 'Colombia', '6tem9Countries' ),
		'KM' => __( 'Comoros', '6tem9Countries' ),
		'CG' => __( 'Congo (Brazzaville)', '6tem9Countries' ),
		'CD' => __( 'Congo (Kinshasa)', '6tem9Countries' ),
		'CK' => __( 'Cook Islands', '6tem9Countries' ),
		'CR' => __( 'Costa Rica', '6tem9Countries' ),
		'HR' => __( 'Croatia', '6tem9Countries' ),
		'CU' => __( 'Cuba', '6tem9Countries' ),
		'CW' => __( 'Cura&Ccedil;ao', '6tem9Countries' ),
		'CY' => __( 'Cyprus', '6tem9Countries' ),
		'CZ' => __( 'Czech Republic', '6tem9Countries' ),
		'DK' => __( 'Denmark', '6tem9Countries' ),
		'DJ' => __( 'Djibouti', '6tem9Countries' ),
		'DM' => __( 'Dominica', '6tem9Countries' ),
		'DO' => __( 'Dominican Republic', '6tem9Countries' ),
		'EC' => __( 'Ecuador', '6tem9Countries' ),
		'EG' => __( 'Egypt', '6tem9Countries' ),
		'SV' => __( 'El Salvador', '6tem9Countries' ),
		'GQ' => __( 'Equatorial Guinea', '6tem9Countries' ),
		'ER' => __( 'Eritrea', '6tem9Countries' ),
		'EE' => __( 'Estonia', '6tem9Countries' ),
		'ET' => __( 'Ethiopia', '6tem9Countries' ),
		'FK' => __( 'Falkland Islands', '6tem9Countries' ),
		'FO' => __( 'Faroe Islands', '6tem9Countries' ),
		'FJ' => __( 'Fiji', '6tem9Countries' ),
		'FI' => __( 'Finland', '6tem9Countries' ),
		'FR' => __( 'France', '6tem9Countries' ),
		'GF' => __( 'French Guiana', '6tem9Countries' ),
		'PF' => __( 'French Polynesia', '6tem9Countries' ),
		'TF' => __( 'French Southern Territories', '6tem9Countries' ),
		'GA' => __( 'Gabon', '6tem9Countries' ),
		'GM' => __( 'Gambia', '6tem9Countries' ),
		'GE' => __( 'Georgia', '6tem9Countries' ),
		'DE' => __( 'Germany', '6tem9Countries' ),
		'GH' => __( 'Ghana', '6tem9Countries' ),
		'GI' => __( 'Gibraltar', '6tem9Countries' ),
		'GR' => __( 'Greece', '6tem9Countries' ),
		'GL' => __( 'Greenland', '6tem9Countries' ),
		'GD' => __( 'Grenada', '6tem9Countries' ),
		'GP' => __( 'Guadeloupe', '6tem9Countries' ),
		'GT' => __( 'Guatemala', '6tem9Countries' ),
		'GG' => __( 'Guernsey', '6tem9Countries' ),
		'GN' => __( 'Guinea', '6tem9Countries' ),
		'GW' => __( 'Guinea-Bissau', '6tem9Countries' ),
		'GY' => __( 'Guyana', '6tem9Countries' ),
		'HT' => __( 'Haiti', '6tem9Countries' ),
		'HM' => __( 'Heard Island and McDonald Islands', '6tem9Countries' ),
		'HN' => __( 'Honduras', '6tem9Countries' ),
		'HK' => __( 'Hong Kong', '6tem9Countries' ),
		'HU' => __( 'Hungary', '6tem9Countries' ),
		'IS' => __( 'Iceland', '6tem9Countries' ),
		'IN' => __( 'India', '6tem9Countries' ),
		'ID' => __( 'Indonesia', '6tem9Countries' ),
		'IR' => __( 'Iran', '6tem9Countries' ),
		'IQ' => __( 'Iraq', '6tem9Countries' ),
		'IE' => __( 'Republic of Ireland', '6tem9Countries' ),
		'IM' => __( 'Isle of Man', '6tem9Countries' ),
		'IL' => __( 'Israel', '6tem9Countries' ),
		'IT' => __( 'Italy', '6tem9Countries' ),
		'CI' => __( 'Ivory Coast', '6tem9Countries' ),
		'JM' => __( 'Jamaica', '6tem9Countries' ),
		'JP' => __( 'Japan', '6tem9Countries' ),
		'JE' => __( 'Jersey', '6tem9Countries' ),
		'JO' => __( 'Jordan', '6tem9Countries' ),
		'KZ' => __( 'Kazakhstan', '6tem9Countries' ),
		'KE' => __( 'Kenya', '6tem9Countries' ),
		'KI' => __( 'Kiribati', '6tem9Countries' ),
		'KW' => __( 'Kuwait', '6tem9Countries' ),
		'KG' => __( 'Kyrgyzstan', '6tem9Countries' ),
		'LA' => __( 'Laos', '6tem9Countries' ),
		'LV' => __( 'Latvia', '6tem9Countries' ),
		'LB' => __( 'Lebanon', '6tem9Countries' ),
		'LS' => __( 'Lesotho', '6tem9Countries' ),
		'LR' => __( 'Liberia', '6tem9Countries' ),
		'LY' => __( 'Libya', '6tem9Countries' ),
		'LI' => __( 'Liechtenstein', '6tem9Countries' ),
		'LT' => __( 'Lithuania', '6tem9Countries' ),
		'LU' => __( 'Luxembourg', '6tem9Countries' ),
		'MO' => __( 'Macao S.A.R., China', '6tem9Countries' ),
		'MK' => __( 'Macedonia', '6tem9Countries' ),
		'MG' => __( 'Madagascar', '6tem9Countries' ),
		'MW' => __( 'Malawi', '6tem9Countries' ),
		'MY' => __( 'Malaysia', '6tem9Countries' ),
		'MV' => __( 'Maldives', '6tem9Countries' ),
		'ML' => __( 'Mali', '6tem9Countries' ),
		'MT' => __( 'Malta', '6tem9Countries' ),
		'MH' => __( 'Marshall Islands', '6tem9Countries' ),
		'MQ' => __( 'Martinique', '6tem9Countries' ),
		'MR' => __( 'Mauritania', '6tem9Countries' ),
		'MU' => __( 'Mauritius', '6tem9Countries' ),
		'YT' => __( 'Mayotte', '6tem9Countries' ),
		'MX' => __( 'Mexico', '6tem9Countries' ),
		'FM' => __( 'Micronesia', '6tem9Countries' ),
		'MD' => __( 'Moldova', '6tem9Countries' ),
		'MC' => __( 'Monaco', '6tem9Countries' ),
		'MN' => __( 'Mongolia', '6tem9Countries' ),
		'ME' => __( 'Montenegro', '6tem9Countries' ),
		'MS' => __( 'Montserrat', '6tem9Countries' ),
		'MA' => __( 'Morocco', '6tem9Countries' ),
		'MZ' => __( 'Mozambique', '6tem9Countries' ),
		'MM' => __( 'Myanmar', '6tem9Countries' ),
		'NA' => __( 'Namibia', '6tem9Countries' ),
		'NR' => __( 'Nauru', '6tem9Countries' ),
		'NP' => __( 'Nepal', '6tem9Countries' ),
		'NL' => __( 'Netherlands', '6tem9Countries' ),
		'AN' => __( 'Netherlands Antilles', '6tem9Countries' ),
		'NC' => __( 'New Caledonia', '6tem9Countries' ),
		'NZ' => __( 'New Zealand', '6tem9Countries' ),
		'NI' => __( 'Nicaragua', '6tem9Countries' ),
		'NE' => __( 'Niger', '6tem9Countries' ),
		'NG' => __( 'Nigeria', '6tem9Countries' ),
		'NU' => __( 'Niue', '6tem9Countries' ),
		'NF' => __( 'Norfolk Island', '6tem9Countries' ),
		'KP' => __( 'North Korea', '6tem9Countries' ),
		'NO' => __( 'Norway', '6tem9Countries' ),
		'OM' => __( 'Oman', '6tem9Countries' ),
		'PK' => __( 'Pakistan', '6tem9Countries' ),
		'PS' => __( 'Palestinian Territory', '6tem9Countries' ),
		'PA' => __( 'Panama', '6tem9Countries' ),
		'PG' => __( 'Papua New Guinea', '6tem9Countries' ),
		'PY' => __( 'Paraguay', '6tem9Countries' ),
		'PE' => __( 'Peru', '6tem9Countries' ),
		'PH' => __( 'Philippines', '6tem9Countries' ),
		'PN' => __( 'Pitcairn', '6tem9Countries' ),
		'PL' => __( 'Poland', '6tem9Countries' ),
		'PT' => __( 'Portugal', '6tem9Countries' ),
		'QA' => __( 'Qatar', '6tem9Countries' ),
		'RE' => __( 'Reunion', '6tem9Countries' ),
		'RO' => __( 'Romania', '6tem9Countries' ),
		'RU' => __( 'Russia', '6tem9Countries' ),
		'RW' => __( 'Rwanda', '6tem9Countries' ),
		'BL' => __( 'Saint Barth&eacute;lemy', '6tem9Countries' ),
		'SH' => __( 'Saint Helena', '6tem9Countries' ),
		'KN' => __( 'Saint Kitts and Nevis', '6tem9Countries' ),
		'LC' => __( 'Saint Lucia', '6tem9Countries' ),
		'MF' => __( 'Saint Martin (French part)', '6tem9Countries' ),
		'SX' => __( 'Saint Martin (Dutch part)', '6tem9Countries' ),
		'PM' => __( 'Saint Pierre and Miquelon', '6tem9Countries' ),
		'VC' => __( 'Saint Vincent and the Grenadines', '6tem9Countries' ),
		'SM' => __( 'San Marino', '6tem9Countries' ),
		'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', '6tem9Countries' ),
		'SA' => __( 'Saudi Arabia', '6tem9Countries' ),
		'SN' => __( 'Senegal', '6tem9Countries' ),
		'RS' => __( 'Serbia', '6tem9Countries' ),
		'SC' => __( 'Seychelles', '6tem9Countries' ),
		'SL' => __( 'Sierra Leone', '6tem9Countries' ),
		'SG' => __( 'Singapore', '6tem9Countries' ),
		'SK' => __( 'Slovakia', '6tem9Countries' ),
		'SI' => __( 'Slovenia', '6tem9Countries' ),
		'SB' => __( 'Solomon Islands', '6tem9Countries' ),
		'SO' => __( 'Somalia', '6tem9Countries' ),
		'ZA' => __( 'South Africa', '6tem9Countries' ),
		'GS' => __( 'South Georgia/Sandwich Islands', '6tem9Countries' ),
		'KR' => __( 'South Korea', '6tem9Countries' ),
		'SS' => __( 'South Sudan', '6tem9Countries' ),
		'ES' => __( 'Spain', '6tem9Countries' ),
		'LK' => __( 'Sri Lanka', '6tem9Countries' ),
		'SD' => __( 'Sudan', '6tem9Countries' ),
		'SR' => __( 'Suriname', '6tem9Countries' ),
		'SJ' => __( 'Svalbard and Jan Mayen', '6tem9Countries' ),
		'SZ' => __( 'Swaziland', '6tem9Countries' ),
		'SE' => __( 'Sweden', '6tem9Countries' ),
		'CH' => __( 'Switzerland', '6tem9Countries' ),
		'SY' => __( 'Syria', '6tem9Countries' ),
		'TW' => __( 'Taiwan', '6tem9Countries' ),
		'TJ' => __( 'Tajikistan', '6tem9Countries' ),
		'TZ' => __( 'Tanzania', '6tem9Countries' ),
		'TH' => __( 'Thailand', '6tem9Countries' ),
		'TL' => __( 'Timor-Leste', '6tem9Countries' ),
		'TG' => __( 'Togo', '6tem9Countries' ),
		'TK' => __( 'Tokelau', '6tem9Countries' ),
		'TO' => __( 'Tonga', '6tem9Countries' ),
		'TT' => __( 'Trinidad and Tobago', '6tem9Countries' ),
		'TN' => __( 'Tunisia', '6tem9Countries' ),
		'TR' => __( 'Turkey', '6tem9Countries' ),
		'TM' => __( 'Turkmenistan', '6tem9Countries' ),
		'TC' => __( 'Turks and Caicos Islands', '6tem9Countries' ),
		'TV' => __( 'Tuvalu', '6tem9Countries' ),
		'UG' => __( 'Uganda', '6tem9Countries' ),
		'UA' => __( 'Ukraine', '6tem9Countries' ),
		'AE' => __( 'United Arab Emirates', '6tem9Countries' ),
		'GB' => __( 'United Kingdom (UK)', '6tem9Countries' ),
		'US' => __( 'United States (US)', '6tem9Countries' ),
		'UY' => __( 'Uruguay', '6tem9Countries' ),
		'UZ' => __( 'Uzbekistan', '6tem9Countries' ),
		'VU' => __( 'Vanuatu', '6tem9Countries' ),
		'VA' => __( 'Vatican', '6tem9Countries' ),
		'VE' => __( 'Venezuela', '6tem9Countries' ),
		'VN' => __( 'Vietnam', '6tem9Countries' ),
		'WF' => __( 'Wallis and Futuna', '6tem9Countries' ),
		'EH' => __( 'Western Sahara', '6tem9Countries' ),
		'WS' => __( 'Western Samoa', '6tem9Countries' ),
		'YE' => __( 'Yemen', '6tem9Countries' ),
		'ZM' => __( 'Zambia', '6tem9Countries' ),
		'ZW' => __( 'Zimbabwe', '6tem9Countries' )
	);
}

# -----------------------------------------------------------------------------------------------------
function fListCountries( $tabOption )
{
	// Tableau par defaut
	$tabDefaultOption = array(
		'name' => 'pays',
		'id' => 'pays',
		'selected' => '',
		'style' => '',
		'autre' => ''
	);
	
	// Fusionne le tableau des variables passe en argument avec le tableau des variables par defaut
	$tabOption = array_replace( $tabDefaultOption, $tabOption );

	$tabCountries = fTableCountries();
	$output = '<select id="' . $tabOption['id'] . '" name="' . $tabOption['name'] . '" class="' . $tabOption['style'] . ' ' . $tabOption['autre'] . '"> ';
	$output  .= '<option>' . _('Select a country', '6tem9Fonction') . '</option>';
	foreach ( $tabCountries as $key => $value ) 
	{
		$output  .= '<option value="' . $key . '" ' . selected( $tabOption['selected'], $key, false ) . '>' . $value . '</option>';
	}
	$output .= '</select>';

	return $output;
}

# -----------------------------------------------------------------------------------------------------
# Retourne un tableau associatif des langue de Polylang "langue ISO" => "Texte langue"
function fTableauLanguePolylang()
{
	$tabIntituleLangue = array(
		'af' => 'Afrikaans',
		'ar' => 'العربية', 'rtl',
		'az' => 'Azərbaycan',
		'be' => 'Беларуская мова',
		'bg' => 'български',
		'bs' => 'Bosanski',
		'ca' => 'Català',
		'cs' => 'Čeština',
		'cy' => 'Cymraeg',
		'da' => 'Dansk',
		'de' => 'Deutsch',
		'de' => 'Deutsch',
		'el' => 'Ελληνικά',
		'en' => 'English',
		'en' => 'English',
		'en' => 'English',
		'en' => 'English',
		'eo' => 'Esperanto',
		'es' => 'Español',
		'es' => 'Español',
		'es' => 'Español',
		'es' => 'Español',
		'es' => 'Español',
		'et' => 'Eesti',
		'eu' => 'Euskara',
		'fa' => 'فارسی', 'rtl',
		'fa' => 'فارسی', 'rtl',
		'fi' => 'Suomi',
		'fo' => 'Føroyskt',
		'fr' => 'Français',
		'fy' => 'Frysk',
		'gd' => 'Gàidhlig',
		'gl' => 'Galego',
		'haz' => 'هزاره گی', 'rtl',
		'he' => 'עברית', 'rtl',
		'hi' => 'हिन्दी',
		'hr' => 'Hrvatski',
		'hu' => 'Magyar',
		'id' => 'Bahasa Indonesia',
		'is' => 'Íslenska',
		'it' => 'Italiano',
		'ja' => '日本語',
		'jv' => 'Basa Jawa',
		'ka' => 'ქართული',
		'kk' => 'Қазақ тілі',
		'ko' => '한국어',
		'ku' => 'کوردی', 'rtl',
		'lo' => 'ພາສາລາວ',
		'lt' => 'Lietuviškai',
		'lv' => 'Latviešu valoda',
		'mk' => 'македонски јазик',
		'mn' => 'Монгол хэл',
		'ms' => 'Bahasa Melayu',
		'my' => 'ဗမာစာ',
		'nb' => 'Norsk Bokmål',
		'ne' => 'नेपाली',
		'nl' => 'Nederlands',
		'nn' => 'Norsk Nynorsk',
		'pl' => 'Polski',
		'pt' => 'Português',
		'pt' => 'Português',
		'ro' => 'Română',
		'ru' => 'Русский',
		'si' => 'සිංහල',
		'sk' => 'Slovenčina',
		'sl' => 'Slovenščina',
		'so' => 'Af-Soomaali',
		'sq' => 'Shqip',
		'sr' => 'Српски језик',
		'su' => 'Basa Sunda',
		'sv' => 'Svenska',
		'ta' => 'தமிழ்',
		'th' => 'ไทย',
		'tr' => 'Türkçe',
		'ug' => 'Uyƣurqə',
		'uk' => 'Українська',
		'ur' => 'اردو', 'rtl',
		'uz' => 'Oʻzbek',
		'vec' => 'Vèneto',
		'vi' => 'Tiếng Việt',
		'zh' => '中文 (中国)',
		'zh' => '中文 (香港)',
		'zh' => '中文 (台灣)',
	);	
	
	return $tabIntituleLangue;			
}

# -----------------------------------------------------------------------------------------------------
# Convert ISO country code to country name (French)

if ( !function_exists('fCountryIsoToText') )
{
	function fCountryIsoToText( $iso )
	{
		 $tabCountries = array(
		    'AF' => 'Afghanistan',
		    'ZA' => 'Afrique Du Sud',
		    'AX' => 'Åland, Îles',
		    'AL' => 'Albanie',
		    'DZ' => 'Algérie',
		    'DE' => 'Allemagne',
		    'AD' => 'Andorre',
		    'AO' => 'Angola',
		    'AI' => 'Anguilla',
		    'AQ' => 'Antarctique',
		    'AG' => 'Antigua-Et-Barbuda',
		    'SA' => 'Arabie Saoudite',
		    'AR' => 'Argentine',
		    'AM' => 'Arménie',
		    'AW' => 'Aruba',
		    'AU' => 'Australie',
		    'AT' => 'Autriche',
		    'AZ' => 'Azerbaïdjan',
		    'BS' => 'Bahamas',
		    'BH' => 'Bahreïn',
		    'BD' => 'Bangladesh',
		    'BB' => 'Barbade',
		    'BY' => 'Bélarus',
		    'BE' => 'Belgique',
		    'BZ' => 'Belize',
		    'BJ' => 'Bénin',
		    'BM' => 'Bermudes',
		    'BT' => 'Bhoutan',
		    'BO' => 'Bolivie, L\'état Plurinational De',
		    'BQ' => 'Bonaire, Saint-Eustache Et Saba',
		    'BA' => 'Bosnie-Herzégovine',
		    'BW' => 'Botswana',
		    'BV' => 'Bouvet, Île',
		    'BR' => 'Brésil',
		    'BN' => 'Brunei Darussalam',
		    'BG' => 'Bulgarie',
		    'BF' => 'Burkina Faso',
		    'BI' => 'Burundi',
		    'KY' => 'Caïmans, Îles',
		    'KH' => 'Cambodge',
		    'CM' => 'Cameroun',
		    'CA' => 'Canada',
		    'CV' => 'Cap-Vert',
		    'CF' => 'Centrafricaine, République',
		    'CL' => 'Chili',
		    'CN' => 'Chine',
		    'CX' => 'Christmas, Île',
		    'CY' => 'Chypre',
		    'CC' => 'Cocos (Keeling), Îles',
		    'CO' => 'Colombie',
		    'KM' => 'Comores',
		    'CG' => 'Congo',
		    'CD' => 'Congo, La République Démocratique Du',
		    'CK' => 'Cook, Îles',
		    'KR' => 'Corée, République De',
		    'KP' => 'Corée, République Populaire Démocratique De',
		    'CR' => 'Costa Rica',
		    'CI' => 'Côte D\'ivoire',
		    'HR' => 'Croatie',
		    'CU' => 'Cuba',
		    'CW' => 'Curaçao',
		    'DK' => 'Danemark',
		    'DJ' => 'Djibouti',
		    'DO' => 'Dominicaine, République',
		    'DM' => 'Dominique',
		    'EG' => 'Égypte',
		    'SV' => 'El Salvador',
		    'AE' => 'Émirats Arabes Unis',
		    'EC' => 'Équateur',
		    'ER' => 'Érythrée',
		    'ES' => 'Espagne',
		    'EE' => 'Estonie',
		    'US' => 'États-Unis',
		    'ET' => 'Éthiopie',
		    'FK' => 'Falkland, Îles (Malvinas)',
		    'FO' => 'Féroé, Îles',
		    'FJ' => 'Fidji',
		    'FI' => 'Finlande',
		    'FR' => 'France',
		    'GA' => 'Gabon',
		    'GM' => 'Gambie',
		    'GE' => 'Géorgie',
		    'GS' => 'Géorgie Du Sud-Et-Les Îles Sandwich Du Sud',
		    'GH' => 'Ghana',
		    'GI' => 'Gibraltar',
		    'GR' => 'Grèce',
		    'GD' => 'Grenade',
		    'GL' => 'Groenland',
		    'GP' => 'Guadeloupe',
		    'GU' => 'Guam',
		    'GT' => 'Guatemala',
		    'GG' => 'Guernesey',
		    'GN' => 'Guinée',
		    'GW' => 'Guinée-Bissau',
		    'GQ' => 'Guinée Équatoriale',
		    'GY' => 'Guyana',
		    'GF' => 'Guyane Française',
		    'HT' => 'Haïti',
		    'HM' => 'Heard-Et-Îles Macdonald, Île',
		    'HN' => 'Honduras',
		    'HK' => 'Hong Kong',
		    'HU' => 'Hongrie',
		    'IM' => 'Île De Man',
		    'UM' => 'Îles Mineures Éloignées Des États-Unis',
		    'VG' => 'Îles Vierges Britanniques',
		    'VI' => 'Îles Vierges Des États-Unis',
		    'IN' => 'Inde',
		    'ID' => 'Indonésie',
		    'IR' => 'Iran, République Islamique D\'',
		    'IQ' => 'Iraq',
		    'IE' => 'Irlande',
		    'IS' => 'Islande',
		    'IL' => 'Israël',
		    'IT' => 'Italie',
		    'JM' => 'Jamaïque',
		    'JP' => 'Japon',
		    'JE' => 'Jersey',
		    'JO' => 'Jordanie',
		    'KZ' => 'Kazakhstan',
		    'KE' => 'Kenya',
		    'KG' => 'Kirghizistan',
		    'KI' => 'Kiribati',
		    'KW' => 'Koweït',
		    'LA' => 'Lao, République Démocratique Populaire',
		    'LS' => 'Lesotho',
		    'LV' => 'Lettonie',
		    'LB' => 'Liban',
		    'LR' => 'Libéria',
		    'LY' => 'Libye',
		    'LI' => 'Liechtenstein',
		    'LT' => 'Lituanie',
		    'LU' => 'Luxembourg',
		    'MO' => 'Macao',
		    'MK' => 'Macédoine, L\'ex-République Yougoslave De',
		    'MG' => 'Madagascar',
		    'MY' => 'Malaisie',
		    'MW' => 'Malawi',
		    'MV' => 'Maldives',
		    'ML' => 'Mali',
		    'MT' => 'Malte',
		    'MP' => 'Mariannes Du Nord, Îles',
		    'MA' => 'Maroc',
		    'MH' => 'Marshall, Îles',
		    'MQ' => 'Martinique',
		    'MU' => 'Maurice',
		    'MR' => 'Mauritanie',
		    'YT' => 'Mayotte',
		    'MX' => 'Mexique',
		    'FM' => 'Micronésie, États Fédérés De',
		    'MD' => 'Moldova, République De',
		    'MC' => 'Monaco',
		    'MN' => 'Mongolie',
		    'ME' => 'Monténégro',
		    'MS' => 'Montserrat',
		    'MZ' => 'Mozambique',
		    'MM' => 'Myanmar',
		    'NA' => 'Namibie',
		    'NR' => 'Nauru',
		    'NP' => 'Népal',
		    'NI' => 'Nicaragua',
		    'NE' => 'Niger',
		    'NG' => 'Nigéria',
		    'NU' => 'Niué',
		    'NF' => 'Norfolk, Île',
		    'NO' => 'Norvège',
		    'NC' => 'Nouvelle-Calédonie',
		    'NZ' => 'Nouvelle-Zélande',
		    'IO' => 'Océan Indien, Territoire Britannique De L\'',
		    'OM' => 'Oman',
		    'UG' => 'Ouganda',
		    'UZ' => 'Ouzbékistan',
		    'PK' => 'Pakistan',
		    'PW' => 'Palaos',
		    'PS' => 'Palestinien Occupé, Territoire',
		    'PA' => 'Panama',
		    'PG' => 'Papouasie-Nouvelle-Guinée',
		    'PY' => 'Paraguay',
		    'NL' => 'Pays-Bas',
		    'PE' => 'Pérou',
		    'PH' => 'Philippines',
		    'PN' => 'Pitcairn',
		    'PL' => 'Pologne',
		    'PF' => 'Polynésie Française',
		    'PR' => 'Porto Rico',
		    'PT' => 'Portugal',
		    'QA' => 'Qatar',
		    'RE' => 'Réunion',
		    'RO' => 'Roumanie',
		    'GB' => 'Royaume-Uni',
		    'RU' => 'Russie, Fédération De',
		    'RW' => 'Rwanda',
		    'EH' => 'Sahara Occidental',
		    'BL' => 'Saint-Barthélemy',
		    'SH' => 'Sainte-Hélène, Ascension Et Tristan Da Cunha',
		    'LC' => 'Sainte-Lucie',
		    'KN' => 'Saint-Kitts-Et-Nevis',
		    'SM' => 'Saint-Marin',
		    'MF' => 'Saint-Martin(Partie Française)',
		    'SX' => 'Saint-Martin (Partie Néerlandaise)',
		    'PM' => 'Saint-Pierre-Et-Miquelon',
		    'VA' => 'Saint-Siège (État De La Cité Du Vatican)',
		    'VC' => 'Saint-Vincent-Et-Les Grenadines',
		    'SB' => 'Salomon, Îles',
		    'WS' => 'Samoa',
		    'AS' => 'Samoa Américaines',
		    'ST' => 'Sao Tomé-Et-Principe',
		    'SN' => 'Sénégal',
		    'RS' => 'Serbie',
		    'SC' => 'Seychelles',
		    'SL' => 'Sierra Leone',
		    'SG' => 'Singapour',
		    'SK' => 'Slovaquie',
		    'SI' => 'Slovénie',
		    'SO' => 'Somalie',
		    'SD' => 'Soudan',
		    'SS' => 'Soudan Du Sud',
		    'LK' => 'Sri Lanka',
		    'SE' => 'Suède',
		    'CH' => 'Suisse',
		    'SR' => 'Suriname',
		    'SJ' => 'Svalbard Et Île Jan Mayen',
		    'SZ' => 'Swaziland',
		    'SY' => 'Syrienne, République Arabe',
		    'TJ' => 'Tadjikistan',
		    'TW' => 'Taïwan, Province De Chine',
		    'TZ' => 'Tanzanie, République-Unie De',
		    'TD' => 'Tchad',
		    'CZ' => 'Tchèque, République',
		    'TF' => 'Terres Australes Françaises',
		    'TH' => 'Thaïlande',
		    'TL' => 'Timor-Leste',
		    'TG' => 'Togo',
		    'TK' => 'Tokelau',
		    'TO' => 'Tonga',
		    'TT' => 'Trinité-Et-Tobago',
		    'TN' => 'Tunisie',
		    'TM' => 'Turkménistan',
		    'TC' => 'Turks-Et-Caïcos, Îles',
		    'TR' => 'Turquie',
		    'TV' => 'Tuvalu',
		    'UA' => 'Ukraine',
		    'UY' => 'Uruguay',
		    'VU' => 'Vanuatu',
		    'VE' => 'Venezuela, République Bolivarienne Du',
		    'VN' => 'Viet Nam',
		    'WF' => 'Wallis Et Futuna',
		    'YE' => 'Yémen',
		    'ZM' => 'Zambie',
		    'ZW' => 'Zimbabwe',
		);

		return $tabCountries[$iso];
		// return var_dump(Locale::getDisplayRegion('-' . $country, $language));
	}
}

# -----------------------------------------------------------------------------------------------------
# Retourne true si le plungin est active
if ( !function_exists('fPluginIsActivate') )
{
	function fPluginIsActivate( $plugin ) 
	{
		$blog_plugins = get_option( 'active_plugins', array() );
		$site_plugins = get_site_option( 'active_sitewide_plugins', array() );

		if ( in_array( $plugin, $blog_plugins ) || isset( $site_plugins[$plugin] ) )
			return true;
		else
			return false;
	}
}

# -----------------------------------------------------------------------------------------------------
if ( !function_exists('fUrlCourante') )
{
    function fUrlCourante( $mode = 'base' ) 
    {
        $url = 'http'.(is_ssl() ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        switch( $mode )
        {
            case 'raw' :  
                return $url; 
                break;
            case 'base' :   
            	$explodedUrl = explode('?', $url);
                return reset($explodedUrl); 
                break;
            case 'uri' :    
                $exp = explode( '?', $url );
                return trim( str_replace( home_url(), '', reset( $exp ) ), '/' ); 
                break;
            default:  
                return false;
        }
    }

}

# -----------------------------------------------------------------------------------------------------
if ( !function_exists('fReditectionPageProtege') )
{
	function fReditectionPageProtege()
	{
	    global $tabPageRestreinte;
	    global $pageAtterissage;
	    $tabPageRestreinte = empty($tabPageRestreinte) ? array() : $tabPageRestreinte;
	    $pageAtterissage = empty($pageAtterissage) || $pageAtterissage=='' ? home_url() : $pageAtterissage;
	    if ( in_array( fUrlCourante('base'), $tabPageRestreinte ) )
	        header( 'Location:' . $pageAtterissage );
	}
}
// add_action( 'parse_request', 'fReditectionPageProtege', 1 );

# -----------------------------------------------------------------------------------------------------
# Necessite l'activation du plugin de compteur de vues
if ( !function_exists('fArticlePlusLu') )
{
	function fArticlePlusLu($mode = 'post', $limit = 3) 
	{
		global $wpdb;
		$views_options = get_option('views_options');

		if (!empty($mode) && $mode != 'both') 
			$where = "post_type = '$mode'";
		else 
			$where = '1=1';
		
		$most_viewed = $wpdb->get_results("SELECT $wpdb->posts.*, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_date < '".date('Y-m-d')."' AND post_date > '". date('Y-').(date('m')-1).date('-d') ."' AND $where AND post_status = 'publish' AND meta_key = 'views' AND post_password = '' ORDER BY views DESC LIMIT $limit");
		//$most_viewed = $wpdb->get_results("SELECT DISTINCT $wpdb->posts.*, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_date < '".current_time('mysql')."' AND $where AND post_status = 'publish' AND meta_key = 'views' AND post_password = '' ORDER BY views DESC LIMIT $limit");
		if ($most_viewed) 
		{
			foreach ($most_viewed as $post) 
			{
				$post->views = intval($post->views);
				$tabArticle[] = $post;
			}			
		} 
		else 
		{
			$tabArticle = null;
		}

		return $tabArticle;
	}
}

# -----------------------------------------------------------------------------------------------------
# Retour les x articles les plus commentés sur les 30 derniers jours
if ( !function_exists('fArticlePlusCommente') )
{
	function fArticlePlusCommente($mode = 'post', $limit = 3) 
	{
		global $wpdb;

		if (!empty($mode) && $mode != 'both') 
			$where = "post_type = '$mode'";
		else 
			$where = '1=1';
		
		$most_comment = $wpdb->get_results("SELECT $wpdb->posts.* FROM $wpdb->posts WHERE post_date < '".date('Y-m-d')."' AND post_date > '". date('Y-').(date('m')-1).date('-d') ."' AND $where AND post_status = 'publish' ORDER BY comment_count DESC LIMIT $limit");
		//$most_comment = $wpdb->get_results("SELECT DISTINCT $wpdb->posts.* FROM $wpdb->posts WHERE post_date < '".current_time('mysql')."' AND $where AND post_status = 'publish' ORDER BY comment_count DESC LIMIT $limit");
		if ($most_comment) 
		{
			foreach ($most_comment as $post) 
			{
				$tabArticle[] = $post;
			}			
		} 
		else 
		{
			$tabArticle = null;
		}

		return $tabArticle;
	}
}

# =======================================================================================================================================
# LISTE GENERIQUE : nombre, jour, mois, annee

# -----------------------------------------------------------------------------------------------------
# liste de nombre
if ( !function_exists('fListeNombre') )
{
	function fListeNombre( $name="", $id="", $debut="", $fin="", $valeurSelectionne="", $style="", $avecZero=0, $obligatoire=false )
	{
		$arguments = func_get_args();
		if ( sizeof($arguments)==1 && is_array($arguments[0]) )
		{
			// Un tableau est fournis en parametre
			// Tableau par defaut
			$tabDefautArgument = array(
				'name' => 'selectNombre',
				'id' => 'selectNombre',
				'debut' => 0,
				'fin' => 20,
				'valeurSelectionne' => '',
				'style' => '',
				'avecZero' => 0,
				'intitulePremierChoix' => '',
				'valeurPremierChoix' => '',
				'increment' => 1,
				'obligatoire' => false,
			);
			
			// Fusionne le tableau des variables passe en argument avec le tableau des variables par defaut
			$tabArgument = array_replace( $tabDefautArgument, $arguments[0] );

			$name = $tabArgument['name'];
			$id = $tabArgument['id'];
			$valeurSelectionne = $tabArgument['valeurSelectionne'];
			$debut = $tabArgument['debut'];
			$fin = $tabArgument['fin'];
			$style = $tabArgument['style'];
			$avecZero = $tabArgument['avecZero'];
			$intitulePremierChoix = $tabArgument['intitulePremierChoix'];
			$valeurPremierChoix = $tabArgument['valeurPremierChoix'];
			$increment = $tabArgument['increment'];
			$obligatoire = $tabArgument['obligatoire'];
		}
		else
		{
			$name = isset($arguments[0]) ? $arguments[0] : '';
			$id = isset($arguments[1]) ? $arguments[1] : 0;
			$debut = isset($arguments[2]) ? $arguments[2] : 0;
			$fin = isset($arguments[3]) ? $arguments[3] : 0;
			$valeurSelectionne = isset($arguments[4]) ? $arguments[4] : 0;
			$style = isset($arguments[5]) ? $arguments[5] : 0;
			$avecZero = isset($arguments[6]) ? $arguments[6] : 0;
			$intitulePremierChoix = isset($arguments[7]) ? $arguments[7] : '';
			$valeurPremierChoix = isset($arguments[8]) ? $arguments[8] : '';
			$increment = isset($arguments[9]) ? $arguments[9] : 1;
			$obligatoire = isset($arguments[10]) ? $arguments[10] : false;
		}	
		$required = $obligatoire ? ' required="required"' : '';
		$retour = '<select id="' . $id . '" name="' . $name . '" class="' . $style . '"' . $required . '> ';
	
		if ( $intitulePremierChoix!='' )
			$retour  .= '<option value="' . $valeurPremierChoix . '">' . $intitulePremierChoix . '</option>';

		while ( $debut<=$fin )
		{
			$valeur =  $debut<10 && $avecZero ? '0'.$debut : $debut;
			$selected = $valeurSelectionne==$valeur ? ' selected' : '';
			$retour .= '<option value="'. $valeur .'"'. $selected .'>'. $valeur .'</option>'; 
			$debut = $debut + $increment;
		}
		$retour .= "</select>";
		
		return $retour;
	}
}

# -------------------------------------------------------------------------------------------------------------------------------
# Genere un liste HTML d'annee 
if ( !function_exists('fListeAnnee') )
{
	function fListeAnnee( $name, $id, $valeurSelectionne="", $nbAnneeAuDessus=2, $anneeDepart=1970, $style="", $obligatoire=false )
	{
		$arguments = func_get_args();
		if ( sizeof($arguments)==1 && is_array($arguments[0]) )
		{
			// Un tableau est fournis en parametre
			// Tableau par defaut
			$tabDefautArgument = array(
				'name' => 'selectAnnee',
				'id' => 'selectAnnee',
				'valeurSelectionne' => '',
				'nbAnneeAuDessus' => 2,
				'anneeDepart' => 1979,
				'style' => '',
				'intitulePremierChoix' => '',
				'valeurPremierChoix' => '',
				'obligatoire' => false,
			);
			
			// Fusionne le tableau des variables passe en argument avec le tableau des variables par defaut
			$tabArgument = array_replace( $tabDefautArgument, $arguments[0] );

			$name = $tabArgument['name'];
			$id = $tabArgument['id'];
			$valeurSelectionne = $tabArgument['valeurSelectionne'];
			$nbAnneeAuDessus = $tabArgument['nbAnneeAuDessus'];
			$anneeDepart = $tabArgument['anneeDepart'];
			$style = $tabArgument['style'];
			$intitulePremierChoix = $tabArgument['intitulePremierChoix'];
			$valeurPremierChoix = $tabArgument['valeurPremierChoix'];
			$obligatoire = $tabArgument['obligatoire'];
		}
		else
		{
			$name = isset($arguments[0]) ? $arguments[0] : '';
			$id = isset($arguments[1]) ? $arguments[1] : 0;
			$valeurSelectionne = isset($arguments[2]) ? $arguments[2] : 0;
			$nbAnneeAuDessus = isset($arguments[3]) ? $arguments[3] : 0;
			$anneeDepart = isset($arguments[4]) ? $arguments[4] : 0;
			$style = isset($arguments[5]) ? $arguments[5] : 0;
			$intitulePremierChoix = isset($arguments[6]) ? $arguments[6] : '';
			$valeurPremierChoix = isset($arguments[7]) ? $arguments[7] : '';
			$obligatoire = isset($arguments[8]) ? $arguments[8] : false;
		}		

		$anneeCourante = date("Y");
		$anneeCourante = $anneeCourante + $nbAnneeAuDessus;
		
		$required = $obligatoire ? ' required="required"' : '';
		$retour = '<select id="' . $id . '" name="' . $name . '" class="' . $style . '"' . $required . '> ';

		if ( $intitulePremierChoix!='' )
			$retour  .= '<option value="' . $valeurPremierChoix . '">' . $intitulePremierChoix . '</option>';

		while ( $anneeCourante>=$anneeDepart )
		{
			if ( $anneeCourante==$valeurSelectionne ) 
				$selected='selected="selected"';
			else 
				$selected="";
			$retour .= '<option value="' . $anneeCourante . '" ' . $selected . '>' . $anneeCourante . '</option>';
			$anneeCourante --;
		}	
		$retour .= "</select>";	
		return $retour;
	}
}

# -------------------------------------------------------------------------------------------------------------------------------
# Genere un liste HTML contenant la liste des mois de l'annee
if ( !function_exists('fListeMois') )
{
	function fListeMois( $name="", $id="", $valeurSelectionne="", $style="", $obligatoire=false )
	{
		$arguments = func_get_args();
		if ( sizeof($arguments)==1 && is_array($arguments[0]) )
		{
			// Un tableau est fournis en parametre
			// Tableau par defaut
			$tabDefautArgument = array(
				'name' => 'selectMois',
				'id' => 'selectMois',
				'valeurSelectionne' => '',
				'style' => '',
				'intitulePremierChoix' => '',
				'valeurPremierChoix' => '',
				'obligatoire' => false,
			);
			
			// Fusionne le tableau des variables passe en argument avec le tableau des variables par defaut
			$tabArgument = array_replace( $tabDefautArgument, $arguments[0] );

			$name = $tabArgument['name'];
			$id = $tabArgument['id'];
			$valeurSelectionne = $tabArgument['valeurSelectionne'];
			$style = $tabArgument['style'];
			$intitulePremierChoix = $tabArgument['intitulePremierChoix'];
			$valeurPremierChoix = $tabArgument['valeurPremierChoix'];
			$obligatoire = $tabArgument['obligatoire'];
		}
		else
		{
			$name = isset($arguments[0]) ? $arguments[0] : '';
			$id = isset($arguments[1]) ? $arguments[1] : 0;
			$valeurSelectionne = isset($arguments[2]) ? $arguments[2] : 0;
			$style = isset($arguments[5]) ? $arguments[5] : 0;
			$intitulePremierChoix = isset($arguments[6]) ? $arguments[6] : '';
			$valeurPremierChoix = isset($arguments[7]) ? $arguments[7] : '';
			$obligatoire = isset($arguments[8]) ? $arguments[8] : false;
		}		

		$janvier = $fevrier = $mars = $avril = $mai = $juin = $juillet = $aout = $septembre = $octobre = $novembre = $decembre = '';
		switch ($valeurSelectionne)
		{
			case '01': $janvier="selected='selected'"; break;
			case '02': $fevrier="selected='selected'"; break;
			case '03': $mars="selected='selected'"; break;
			case '04': $avril="selected='selected'"; break;
			case '05': $mai="selected='selected'"; break;
			case '06': $juin="selected='selected'"; break;
			case '07': $juillet="selected='selected'"; break;
			case '08': $aout="selected='selected'"; break;
			case '09': $septembre="selected='selected'"; break;
			case '10': $octobre="selected='selected'"; break;
			case '11': $novembre="selected='selected'"; break;
			case '12': $decembre="selected='selected'"; break;
		}
		
		$required = $obligatoire ? ' required="required"' : '';
		$retour = '<select id="' . $id . '" name="' . $name . '" class="' . $style . '"' . $required . '> ';

		if ( $intitulePremierChoix!='' )
			$retour  .= '<option value="' . $valeurPremierChoix . '">' . $intitulePremierChoix . '</option>';

		$retour .= '<option value="01"  ' . $janvier . '>01-jan</option>';
		$retour .= '<option value="02" ' . $fevrier  . '>02-fév</option>';
		$retour .= '<option value="03"  ' . $mars  . '>03-mars</option>';
		$retour .= '<option value="04"  ' . $avril  . '>04-avr</option>';
		$retour .= '<option value="05"  ' . $mai . '>05-mai</option>';
		$retour .= '<option value="06"  ' . $juin  . '>06-juin</option>';
		$retour .= '<option value="07"  ' . $juillet  . '>07-juil</option>';
		$retour .= '<option value="08"  ' . $aout  . '>08-août</option>';
		$retour .= '<option value="09"  ' . $septembre  . '>09-sept</option>';
		$retour .= '<option value="10"  ' . $octobre  . '>10-oct</option>';
		$retour .= '<option value="11"  ' . $novembre  . '>11-nov</option>';
		$retour .= '<option value="12"  ' . $decembre  . '>12-déc</option>';
		$retour .= '</select>	';
		
		return $retour;
	}
}


# -----------------------------------------------------------------------------------------------------
# Ajoute le code HTML permettant de rendre les vidéos ajoutées dans l'éditeur Wordpress compatible responsive Bootstrap
if ( !function_exists('fEmbedHtmlFront') )
{

	function fEmbedHtmlFront($html, $url, $attr)
	{
	    # Services we want to show in 16/9 display
	    $hosts = array( 
	    	'animoto.com', 
	    	'blip.tv', 
	    	'collegehumor.com', 
	    	'money.cnn.com', 
	    	'dailymotion.com',  
	    	'funnyordie.com', 
	    	'hulu.com',
	    	'issuu.com', 
	    	'kickstarter.com', 
	    	'mixcloud.com', 
	    	'rdio.com', 
	    	'revision3.com', 
	    	'soundcloud.com', 
	    	'ted.com', 
	    	'vimeo.com', 
	    	'vine.co', 
	    	'wordpress.tv', 
	    	'youtube.com',
	    	'youtu.be'
	    );

	    $isSpecificHost = false;

	    # We check if the current url is from one of the listed services
	    foreach ( $hosts as $host ) 
	    {
	        # If it's a service we want in 16/9
	        if ( strpos( $url, $host ) !== false ) 
	        {
	            $isSpecificHost = true;
	            break;
	        }  
	    }

	    # Specific case for Instagram
	    if ( strpos( $url, 'instagram.com' ) !== false ) 
	    	$html = "<div class='responsive-item instagram-item'>" . $html . "</div>";
	    else if ( strpos( $url, 'spotify.com' ) !== false ) 
	    	$html = "<div class='responsive-item spotify-item'>" . $html . "</div>";
	    else if ( strpos( $url, 'twitter.com' ) !== false ) 
	    	$html = "<div class='responsive-item twitter-item'>" . $html . "</div>";
	    else if ($isSpecificHost)
	    	$html = '<div class="embed-responsive embed-responsive-16by9">' . $html . '</div>';
	    else
	    	$html = "<div class='responsive-item'>" . $html . "</div>";

	    return $html;
	}
}

add_filter('embed_oembed_html', 'fEmbedHtmlFront', 10, 3);

# =======================================================================================================================================
# FONCTON GESTION DE CHAINES DE CHARACTERES

# -----------------------------------------------------------------------------------------------------
if ( !function_exists('fFermetureBaliseHtml') )
{
    function fFermetureBaliseHtml($html) 
    {
        #put all opened tags into an array
        $content = $result;
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];   #put all closed tags into an array
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        # all tags are closed
        if (count($closedtags) == $len_opened) {
            return $html;
        }

        $openedtags = array_reverse($openedtags);
        # close tags
        for ($i=0; $i < $len_opened; $i++) {
        if (!in_array($openedtags[$i], $closedtags))
        {
          $html .= '</'.$openedtags[$i].'>';
        } 
        else 
        {
          unset($closedtags[array_search($openedtags[$i], $closedtags)]);    }
        }  
        return $html;
    } 
}

# -----------------------------------------------------------------------------------------------------
# Supprime la balise more
function fRemoveMoreTag( $content ) 
{
   global $post;
   return str_replace( '<!--more-->', '', $post->post_content );
}

# -----------------------------------------------------------------------------------------------------
if ( !function_exists('fDebutTexte') )
{
    function fDebutTexte( $wordsLength, $content='', $textStyled=0, $deleteLinks=1 ) 
    {    	
		$arguments = func_get_args();
		if ( sizeof($arguments)==1 && is_array($arguments[0]) )
		{
			// Un tableau est fournis en parametre
			// Tableau par defaut
			$tabDefautArgument = array(
				'wordsLength' => 50,
				'content' => '',
				'textStyled' => 0,
				'deleteLinks' => 1,
				'moreTag' => false
			);
			
			// Fusionne le tableau des variables passe en argument avec le tableau des variables par defaut
			$tabArgument = array_replace( $tabDefautArgument, $arguments[0] );

			$wordsLength = $tabArgument['wordsLength'];
			$content = $tabArgument['content'];
			$textStyled = $tabArgument['textStyled'];
			$deleteLinks = $tabArgument['deleteLinks'];
			$moreTag = $tabArgument['moreTag'];
		}
		else
		{
			$wordsLength = isset($arguments[0]) ? $arguments[0] : 50;
			$content = isset($arguments[1]) ? $arguments[1] : '';
			$textStyled = isset($arguments[2]) ? $arguments[2] : 0;
			$deleteLinks = isset($arguments[3]) ? $arguments[3] : 1;
			$moreTag = false;
		}

        if ( $textStyled==1 )
        {
        	$content = apply_filters('the_content', $content);
        	$content = str_replace(']]>', ']]&gt;', $content);
        	// $content = strip_tags($content, wpse_allowedtags()); /*IF you need to allow just certain tags. Delete if all tags are allowed */        	
        }
        else
        {
        	$content = strip_shortcodes( $content ); 
            $content = strip_tags($content);
        	$content = wp_strip_all_tags($content);		
        	$pattern = '`((?:https?|ftp)://\S+[[:alnum:]]/?)`si'; // Suppression des liens.
			$content = preg_replace( $pattern, '', $content );
        }
        
        if ( !$moreTag )
        {        
	        $tokens = array();
	        $excerptOutput = '';
	        $count = 0;

	        // Divide the string into tokens; HTML tags, or words, followed by any whitespace
	        preg_match_all('/(<[^>]+>|[^<>\s]+)\s*/u', $content, $tokens);

	        foreach ( $tokens[0] as $token ) 
	        { 
	            if ( $textStyled==1 )
	            {
		            if ( $count >= $wordsLength && stripos($token, '</p>') !== FALSE )
		            { 
		            	// Limit reached, continue until , ; ? . or ! occur at the end
		                $excerptOutput .= trim($token);
		                break;
		            }
		        }
		        else
		        {
		            if ( $count >= $wordsLength )  
		            { 
		                $excerptOutput .= trim($token);
		                break;
		            }
		        }

	            // Add words to complete sentence
	            $count++;

	            // Append what's left of the token
	            $excerptOutput .= $token;
	        }
	        
	        if ( $textStyled!=1 && $count!=sizeof($tokens[0]) )
	        	$excerptOutput .= '...';

	        $content = trim(force_balance_tags($excerptOutput));
	    }
            
        return $content;
    }
}

# -----------------------------------------------------------------------------------------------------
# Add custom tags to this string
function wpse_allowedtags() {
    return '<script>,<style>,<br>,<em>,<i>,<ul>,<ol>,<li>,<a>,<p>,<img>,<video>,<audio>,<code>,<pre>,<blockquote>'; 
}

# -----------------------------------------------------------------------------------------------------
if ( !function_exists('fExtraitPost') )
{
    function fExtraitPost($tailleTextMaximum, $post, $miseEnforme = 1) 
    {
        $contenuExtrait = $post->post_excerpt;

        $longueurExtrait = strlen($contenuExtrait);        
        if ($longueurExtrait > $tailleTextMaximum)
        {
            $contenuExtrait = substr ($contenuExtrait, 0, $tailleTextMaximum);
            $pos = strrpos($contenuExtrait, " ");
            $contenuExtrait = substr ($contenuExtrait, 0, $pos)."...";
        }
        else
            $contenuExtrait = $contenuExtrait;
            
        if ($miseEnforme == 1)
            $contenuExtrait = "<strong>".$contenuExtrait."</strong>";
                
        $contenuTexte ="";
        
        if ($longueurExtrait<$tailleTextMaximum)
        {
            $tailleTextMaximum = $tailleTextMaximum - $longueurExtrait; 

            $contenuTexte = strip_shortcodes($post->post_content);


            if ($miseEnforme == 1)
            {
                $contenuTexte = strip_tags($contenuTexte,'<p><ul><li><h1><h2>');
                $contenuTexte = preg_replace('#(<[a-z ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', $contenuTexte);
            }
            else
                $contenuTexte = strip_tags($contenuTexte);

            $longueurArticle = strlen($contenuTexte);          
            if ($longueurArticle > $tailleTextMaximum)
            {
                $contenuTexte = substr ($contenuTexte, 0, $tailleTextMaximum);
                $pos = strrpos($contenuTexte, " ");
                $contenuTexte = substr ($contenuTexte, 0, $pos)."...";
            }
            else
                $contenuTexte = $contenuTexte;
            
            $contenuTexte = convert_smilies($contenuTexte);

            if ($miseEnforme == 1)
                $contenuTexte = fFermetureBaliseHtml($contenuTexte);
        }
            
        return $contenuExtrait.$contenuTexte;
    }
}

# -----------------------------------------------------------------------------------------------------
# Supprime les accents
if ( !function_exists('fSuppressionAccent') )
{
	function fSuppressionAccent( $texte )
	{
	    $texte = mb_strtolower($texte, 'UTF-8');
	    $texte = str_replace(
	        array(
	            'à', 'â', 'ä', 'á', 'ã', 'å',
	            'à', 'â', 'ä', 'á', 'ã', 'å',
	            'î', 'ï', 'ì', 'í',
				'î', 'ï', 'ì', 'í', 
	            'ô', 'ö', 'ò', 'ó', 'õ', 'ø',
	            'ô', 'ö', 'ò', 'ó', 'õ', 'ø',
	            'ù', 'û', 'ü', 'ú',
	            'ù', 'û', 'ü', 'ú',
	            'é', 'è', 'ê', 'ë', 'è', 'é',
	            'é', 'è', 'ê', 'ë', 'è', 'é',
	            'ç', 'ÿ', 'ñ', ' ',
	            'ç', 'ÿ', 'ñ'
	        ),
	        array(
	            'a', 'a', 'a', 'a', 'a', 'a',
	            'a', 'a', 'a', 'a', 'a', 'a',
	            'i', 'i', 'i', 'i', 
	            'i', 'i', 'i', 'i', 
	            'o', 'o', 'o', 'o', 'o', 'o', 
	            'o', 'o', 'o', 'o', 'o', 'o', 
	            'u', 'u', 'u', 'u', 
	            'u', 'u', 'u', 'u', 
	            'e', 'e', 'e', 'e', 'e', 'e',
	            'e', 'e', 'e', 'e', 'e', 'e',
	            'c', 'y', 'n', '',
	            'c', 'y', 'n'
	        ),
	        $texte
	    );
	 
	    return $texte;        
	}
}

# -----------------------------------------------------------------------------------------------------
# Convertis les apostrophes en apostrophe correctement affichees
if ( !function_exists('fNetoyageApostrophe') )
{
	function fNetoyageApostrophe( $content )
	{
	    $content = str_replace( array( "'", "`", "’", "& 146;", "& #2019;", "& #8217;", "& apos;", "& amp;apos;", "& #039;" ), "'", $content );
	    return $content;
	}
	add_filter('the_title', 'fNetoyageApostrophe');
	add_filter('the_content', 'fNetoyageApostrophe');
	add_filter('the_excerpt', 'fNetoyageApostrophe');
}

# =======================================================================================================================================
# GESTION DES FLUX RSS

# -----------------------------------------------------------------------------------------------------
#  Ajout des types de contenus aux flux RSS principal
if ( !function_exists('fTypeContenuRSS') )
{
	function fTypeContenuRSS( $qv ) 
	{
	    if ( isset($qv['feed']) && !isset($qv['post_type']) )
	    {
			// Les types de contenu
			$tabOptionDev = get_option('tabOptionDev');
			$tabListeTypeContenuRSS = isset($tabOptionDev['listeTypeContenuRSS']) ? $tabOptionDev['listeTypeContenuRSS'] : array();

			// S'il y a des custom post types dynamiques, on les ajoute
			if ( is_plugin_active( '6tem9PostType/6tem9PostType.php' ))
			{
				$tabDynCPT = get_posts( array( 'numberposts' => -1, 'post_type' => 'dyncpt', 'post_status' => 'publish' ) );

				if ( sizeof($tabDynCPT) > 0 )
				{
					for ( $i=0; $i<sizeof($tabDynCPT); $i++ )
					{
						# Options of dynamic custom post type
						$idDynCPT = $tabDynCPT[$i]->ID;
						$cptSlug = get_post_meta( $idDynCPT, 'cptSlug', true );
						$tabSlugDyn[] = $cptSlug;

					}
					$tabListeTypeContenuRSS = array_merge( $tabListeTypeContenuRSS, $tabSlugDyn );
				}
			}

			$tabTypeContenu = array('page', 'event');
			if ( sizeof($tabListeTypeContenuRSS) > 0 )
			{
				foreach ( $tabListeTypeContenuRSS as $slugCptype => $valeur )
				{	
					$tabTypeContenu[] = $slugCptype;
				}
			}	
			$qv['post_type'] = $tabTypeContenu;
	    }
	    return $qv;
	}
	add_filter( 'request', 'fTypeContenuRSS' );
}

# -----------------------------------------------------------------------------------------------------
# Rajoute l'enclosure image a un item 'article' dans le flux RSS du site en utilisant l'image a la une de l'article comem source
if ( !function_exists('fEclosureImageRSS') )
{
	function fEclosureImageRSS ()
	{
	    global $post;
	    if ( has_post_thumbnail($post->ID) ) 
	    {
	        $url = wp_get_attachment_url( get_post_thumbnail_id() ); // url de la miniature
	        $donnee = strrchr( $url, 'wp-content/' ); // 
	        $ext =  substr($donnee, strripos($donnee, ".")+1 ); // extension du fichier
	        echo "<enclosure url='" . $url . "' length ='" . filesize($donnee) . "'  type='image/" . $ext . "' /> \n <pubDate>" . the_date( 'r T','','', FALSE) . "</pubDate>  \n";
	    }
	}	
	add_action( 'rss_item', 'fEclosureImageRSS' );
	add_action( 'rss2_item', 'fEclosureImageRSS' );
}

# -----------------------------------------------------------------------------------------------------
# Ajout de l'image a la une dans le texte du flux RSS
if ( !function_exists('fAjoutVignetteRSS') )
{
	function fAjoutVignetteRSS( $content ) 
	{
	   global $post;
	   if ( has_post_thumbnail( $post->ID ) )
	   {
	      $content = '<p>' . get_the_post_thumbnail( $post->ID, 'medium' ) . '</p>' . $content;
	   }
	   return $content;
	}
	add_filter( 'the_excerpt_rss', 'fAjoutVignetteRSS' );
	add_filter( 'the_content_feed', 'fAjoutVignetteRSS' );
}

# =======================================================================================================================================
# PAGINATION
/**
 * fPagination($url, $param, $total, $current [, $adj]) appelée à chaque affichage de la pagination
 * @param string $url - URL ou nom de la page appelant la fonction, ex: 'index.php' ou 'http://example.com/'
 * @param string $param - paramètre à ajouter à l'URL, ex: '?page=' ou '&amp;p='
 * @param int $total - nombre total de pages
 * @param int $current - numéro de la page courante
 * @param int $adj (facultatif) - nombre de numéros de chaque côté du numéro de la page courante (défaut : 3)
 * @return string $pagination
 */
if ( !function_exists('fPagination') )
{
	function fPagination($url, $param, $total, $current, $adj=3, $paramPers='', $fullMobile=false, $couplageMoteurRecherche=false)
	{
	    // echo "$url, $param, $total, $current";
	    # Déclaration des variables 
	    $first = 1; // numéro de la première page
	    $prev = $current - 1; // numéro de la page précédente
	    $next = $current + 1; // numéro de la page suivante
	    $n2l = $total - 1; // numéro de l'avant-dernière page (n2l = next to last)
	    $last = $total; // numéro de la dernière page

	    # Initialisation : s'il n'y a pas au moins deux pages, l'affichage reste vide 
	    $pagination = '';

	    # Sinon ... 
	    if ($total > 1)
	    {
	        # -----------------------------------------------------------------------------------
	        # Pagination sur smartphone

	    	// $classSmartphone = $fullMobile ? '' : ' pagination-lg visible-phone';
	    	$classSmartphone = $fullMobile ? '' : ' visible-phone-inline-block hidden-sm hidden-md hidden-lg ';


	        $pagination .= "<div id=\"systemePaginationSmartphone\" class=\"pagination{$classSmartphone} btn-group\" role=\"group\">\n";

	        # Affichage du bouton [début] et [précédent]
	        if ($current == $first) // la page courante est supérieure à 2, le bouton renvoit sur la page dont le numéro est immédiatement inférieur
	        {
	            $pagination .= "<a href='#' class='disabled btn btn-default'><i class='fa fa-angle-double-left'></i></a>";
	            $pagination .= "<a href='#' class='disabled btn btn-default'><i class='fa fa-angle-left'></i></a>";
	        }
	        else // dans tous les autres, cas la page est 1 : désactivation du bouton [précédent]
	        {
	            $pagination .= "<a href=\"{$url}{$param}{$first}{$paramPers}\" class='btn btn-default'><i class='fa fa-angle-double-left'></i></a>";
	            $pagination .= "<a href=\"{$url}{$param}{$prev}{$paramPers}\" class='btn btn-default'><i class='fa fa-angle-left'></i></a>";
	        }

	        # Page courante
	        $onChange = $couplageMoteurRecherche ? '' : " onchange='location = this.options[this.selectedIndex].value;'";
	        $pagination .= "<div class='btn-group' role='group'>";
	        $pagination .= "<a href=\"#\" class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>Page ".$current."  <span class='caret'></span></a>";
	        $pagination .= "<ul id=\"selectPagePagination\" class='dropdown-menu' role='menu' {$onChange}>";
	        for ($i = 1; $i<=$total; $i++)
	        {
	        	$pagination .= 	"<li><a href='{$url}{$param}{$i}{$paramPers}' " . selected( $i, $current, false ) . ">Page ".$i."</a></li>";
	        }
	        $pagination .= "</ul>";	        
	        $pagination .= "</div>\n";

	        # Affichage du bouton [suivant] et [fin]
	        if ($current == $last)
	        {
	            $pagination .= "<a href='#' class=\"inactive disabled btn btn-default\"><i class='fa fa-angle-right'></i></a>\n";
	            $pagination .= "<a href='#' class=\"inactive disabled btn btn-default\"><i class='fa fa-angle-double-right'></i></a>\n";
	        }
	        else
	        {
	            $pagination .= "<a href=\"{$url}{$param}{$next}{$paramPers}\" class=\"btn btn-default\"><i class='fa fa-angle-right'></i></a>\n";
	            $pagination .= "<a href=\"{$url}{$param}{$last}{$paramPers}\" class=\"btn btn-default\"><i class='fa fa-angle-double-right'></i></a>\n";
	        }

	        $pagination .= "</div>";

	        $pagination .= "<div class='spacer'></div>";

	        # -----------------------------------------------------------------------------------
	        # Pagination sur tablette et pc

	        if ( !$fullMobile )
	        {
		        #  Concaténation du <div> d'ouverture à $pagination 
		        $pagination .= "<ul id=\"systemePagination\" class=\"pagination pagination-lg hidden-phone\">\n";

		        # Affichage du bouton [précédent] 
		        if ($current == 2) // la page courante est la 2, le bouton renvoit donc sur la page 1, remarquez qu'il est inutile de mettre ?p=1
		            $pagination .= "<li><a href=\"{$url}{$paramPers}\">&laquo;</a></li>";
		        elseif ($current > 2) // la page courante est supérieure à 2, le bouton renvoit sur la page dont le numéro est immédiatement inférieur
		            $pagination .= "<li><a href=\"{$url}{$param}{$prev}{$paramPers}\">&laquo;</a></li>";
		        else // dans tous les autres, cas la page est 1 : désactivation du bouton [précédent]
		            $pagination .= '<li class="disabled"><a href="#">&laquo;</a></li>';

		        /* 
		        Début affichage des pages, l'exemple reprend le cas de 3 numéros de pages adjacents (par défaut) de chaque côté du numéro courant
		        - CAS 1 : il y a au plus 12 pages, insuffisant pour faire une troncature
		        - CAS 2 : il y a au moins 13 pages, on effectue la troncature pour afficher 11 numéros de pages au total
		        */

		        # CAS 1 
		        if ($total < 7 + ($adj * 2))
		        {
		            # Ajout de la page 1 : on la traite en dehors de la boucle pour n'avoir que index.php au lieu de index.php?p=1 et ainsi éviter le duplicate content 
		            $pagination .= ($current == 1) ? '<li class="active disabled"><a href="#">1</a></li>' : "<li><a href=\"{$url}{$paramPers}\">1</a><li>"; // Opérateur ternaire : (condition) ? 'valeur si vrai' : 'valeur si fausse'

		            #  Pour les pages restantes on utilise une boucle for 
		            for ($i = 2; $i<=$total; $i++)
		            {
		                if ($i == $current) // Le numéro de la page courante est mis en évidence (cf fichier CSS)
		                $pagination .= "<li class=\"active disabled\"><a href='#'>{$i}</a></li>";
		                else // Les autres sont affichés normalement
		                $pagination .= "<li><a href=\"{$url}{$param}{$i}{$paramPers}\">{$i}</a></li>";
		            }
		        }

		        #  CAS 2 : au moins 13 pages, troncature 
		        else
		        {
		            /*
		            Troncature 1 : on se situe dans la partie proche des premières pages, on tronque donc la fin de la pagination.
		            l'affichage sera de neuf numéros de pages à gauche ... deux à droite (cf figure 1)
		            */
		            if ($current < 2 + ($adj * 2))
		            {
		                #  Affichage du numéro de page 1 
		                $pagination .= ($current == 1) ? "<li class=\"active disabled\"><a href=\"#\">1</a></li>" : "<li><a href=\"{$url}{$paramPers}\">1</a></li>";

		                #  puis des huit autres suivants 
		                for ($i = 2; $i < 4 + ($adj * 2); $i++)
		                {
		                if ($i == $current)
		                    $pagination .= "<li class=\"active disabled\"><a href='#'>{$i}</a></li>";
		                    else
		                    $pagination .= "<li><a href=\"{$url}{$param}{$i}{$paramPers}\">{$i}</a></li>";
		                }

		                #  ... pour marquer la troncature 
		                $pagination .= "<li class=\"disabled\" ><a href=\"#\"> ... </a></li>";

		                #  et enfin les deux derniers numéros 
		                $pagination .= "<li><a href=\"{$url}{$param}{$n2l}{$paramPers}\">{$n2l}</a></li>";
		                $pagination .= "<li><a href=\"{$url}{$param}{$total}{$paramPers}\">{$total}</a></li>";
		            }

		            /*
		            Troncature 2 : on se situe dans la partie centrale de notre pagination, on tronque donc le début et la fin de la pagination.
		            l'affichage sera deux numéros de pages à gauche ... sept au centre ... deux à droite (cf figure 2)
		            */
		            elseif ( (($adj * 2) + 1 < $current) && ($current < $total - ($adj * 2)) )
		            {
		                #  Affichage des numéros 1 et 2 
		                $pagination .= "<li><a href=\"{$url}{$paramPers}\">1</a></li>";
		                $pagination .= "<li><a href=\"{$url}{$param}2{$paramPers}\">2</a></li>";

		                $pagination .= "<li class=\"disabled\" ><a href=\"#\"> ... </a></li>";

		                #  les septs du milieu : les trois précédents la page courante, la page courante, puis les trois lui succédant 
		                for ($i = $current - $adj; $i <= $current + $adj; $i++)
		                {
		                    if ($i == $current)
		                    $pagination .= "<li class=\"active disabled\"><a href='#'>{$i}</a></li>";
		                    else
		                    $pagination .= "<li><a href=\"{$url}{$param}{$i}{$paramPers}\">{$i}</a></li>";
		                }

		                $pagination .= "<li class=\"disabled\" ><a href=\"#\"> ... </a></li>";

		                #  et les deux derniers numéros 
		                $pagination .= "<li><a href=\"{$url}{$param}{$n2l}{$paramPers}\">{$n2l}</a></li>";
		                $pagination .= "<li><a href=\"{$url}{$param}{$total}{$paramPers}\">{$total}</a></li>";
		            }

		            /*
		            Troncature 3 : on se situe dans la partie de droite, on tronque donc le début de la pagination.
		            l'affichage sera deux numéros de pages à gauche ... neuf à droite (cf figure 3)
		            */
		            else
		            {
		                #  Affichage des numéros 1 et 2 
		                $pagination .= "<li><a href=\"{$url}\">1</a></li>";
		                $pagination .= "<li><a href=\"{$url}{$param}2{$paramPers}\">2</a></li>";

		                $pagination .= "<li class=\"disabled\" ><a href=\"#\"> ... </a></li>";

		                #  puis des neufs dernières 
		                for ($i = $total - (2 + ($adj * 2)); $i <= $total; $i++)
		                {
		                    if ($i == $current)
		                        $pagination .= "<li class=\"active disabled\"><a href='#'>{$i}</a></li>";
		                    else
		                        $pagination .= "<li><a href=\"{$url}{$param}{$i}{$paramPers}\">{$i}</a></li>";
		                }
		            }
		        }

		        # Affichage du bouton [suivant] 
		        if ($current == $total)
		            $pagination .= "<li class=\"inactive disabled\"><a href='#'>&raquo;</a></li>\n";
		        else
		            $pagination .= "<li><a href=\"{$url}{$param}{$next}{$paramPers}\">&raquo;</a></li>\n";

		        # </div> de fermeture 
		        $pagination .= "</ul>\n";
	    	}
	    }
	    
	    #  Fin de la fonction, renvoi de $pagination au programme 
	    return ($pagination);
	}
}

# -----------------------------------------------------------------------------------------------------
# AFFICHE LES INFORMATIONS RELATIVES A UN POST
# @param : $idPost
# @param : $auteur=0
# @param : $lieu=0
# @param : $date 1 pour afficher la date du post | 0 pour ne plas afficher de date | si une valeur est fournie elle sera affichee 
# @param : $commentaires=1
# @param : $categories=1
# @param : $activerLienCategories=1
# @param : $tags=1
# @param : $tabCategoryPost=array()
# @param : $tabKeywordsPost=array()
# @param : $archiveType='list'
# @param : $lienAuteur=0
# @param : $tabElementsSupplementaires=array() > special meta list
# @param : $public=1 > if public==0 the content is private

if ( !function_exists('fAfficherInfosPost') )
{
	function fAfficherInfosPost()
	{
		$arguments = func_get_args();
		$tabInfoPost = array();

		if ( sizeof($arguments)==1 && is_array($arguments[0]) )
		{
			// Un tableau est fournis en parametre
			// Tableau par defaut
			$tabDefautArgument = array(
				'idPost' => 0,
				'auteur' => 0,
				'afficherLieu' => 1,
				'lieu' => '',
				'date' => 0,
				'dateFormat' => 'j F Y',
				'beforeDate' => '',
				'commentaires' => 0, // Bool
				'categories' => 0, // Bool
				'activerLienCategories' => 1,
				'iconeCategories' => 1,
				'tags' => 0, // Bool
				'iconeTags' => 1,				
				'tabCategoryPost' => array(),
				'tabKeywordsPost' => array(),
				'archiveType' => 'list',
				'lienAuteur' => 0,
				'before' => '<p class="infosPost">',
				'after' => '</p>',
				'tabElementsSupplementaires' => array(),
				'public' => 1,
				'separateur' => '&nbsp;|&nbsp;'
			);
			
			// Fusionne le tableau des variables passe en argument avec le tableau des variables par defaut
			$tabArgument = array_replace( $tabDefautArgument, $arguments[0] );

			$idPost = $tabArgument['idPost'];
			$auteur = $tabArgument['auteur'];
			$afficherLieu = $tabArgument['afficherLieu'];
			$lieu = $tabArgument['lieu'];
			$date = $tabArgument['date'];
			$dateFormat = $tabArgument['dateFormat'];
			$beforeDate = $tabArgument['beforeDate'];
			$commentaires = $tabArgument['commentaires'];
			$categories = $tabArgument['categories'];
			$iconeCategories = $tabArgument['iconeCategories'];
			$activerLienCategories = $tabArgument['activerLienCategories'];
			$tags = $tabArgument['tags'];
			$iconeTags = $tabArgument['iconeTags'];
			$tabCategoryPost = $tabArgument['tabCategoryPost'];
			$tabKeywordsPost = $tabArgument['tabKeywordsPost'];
			$archiveType = $tabArgument['archiveType'];
			$lienAuteur = $tabArgument['lienAuteur'];
			$before = $tabArgument['before'];
			$after = $tabArgument['after'];
			$tabElementsSupplementaires = $tabArgument['tabElementsSupplementaires'];
			$public = $tabArgument['public'];
			$separateur = $tabArgument['separateur'];

		}
		else
		{
			$idPost = isset($arguments[0]) ? $arguments[0] : '';
			$auteur = isset($arguments[1]) ? $arguments[1] : 0;
			$lieu = isset($arguments[2]) ? $arguments[2] : 0;
			$date = isset($arguments[3]) ? $arguments[3] : 0;
			$commentaires = isset($arguments[4]) ? $arguments[4] : 0;
			$categories = isset($arguments[5]) ? $arguments[5] : 0;
			$activerLienCategories = 1;
			$tags = isset($arguments[6]) ? $arguments[6] : 0;
			$tabCategoryPost = isset($arguments[7]) ? $arguments[7] : array();
			$tabKeywordsPost = isset($arguments[8]) ? $arguments[8] : array();
			$archiveType = isset($arguments[9]) ? $arguments[9] : 'list';
			$lienAuteur = isset($arguments[10]) ? $arguments[10] : 0;
			$tabElementsSupplementaires = array();
			$public = isset($arguments[12]) ? $arguments[12] : 1;
			$separateur = isset($arguments[13]) ? $arguments[13] : '&nbsp;|&nbsp;';
			$before = '<p class="infosPost">';
			$after = '</p>';
			$dateFormat = 'j F Y';
			$iconeCategories = 1;
			$iconeTags = 1;
			$beforeDate = '';

		}

		# Si au moins une option est vraie
		if ( $auteur || $date || $commentaires || ($categories && $tabCategoryPost != '' && sizeof($tabCategoryPost) > 0) 
			|| $tags && $tabKeywordsPost != '' && sizeof($tabKeywordsPost) > 0 || ( $lieu != '' && $afficherLieu ) || sizeof($tabElementsSupplementaires)!=0 )
		{

			# Séparateur
			$tabInfoPost = array();
			
			if ( $auteur )
			{
				$nomAuteur = get_the_author_meta( 'display_name', get_post_field( 'post_author', $idPost ) );
				$idAuteur = get_the_author_meta( 'ID', get_post_field( 'post_author', $idPost ) );

				$tabInfoPost[] = '<i class="fa fa-user"></i> ' . $nomAuteur;

				if ( $lienAuteur )
					$tabInfoPost[] = '</a>';
			}

			if ( $date )
			{
				$date = $date==1 && $date!=0 ? get_the_time( $dateFormat, $idPost ) : $date;
				$tabInfoPost[] = '<i class="fa fa-clock-o"></i> ' . $beforeDate . $date;
			}

			if ( $lieu != '' && $afficherLieu )
			{
				$tabInfoPost[] = '<i class="fa fa-map-marker"></i> ' . $lieu;					
			}

			if ( $commentaires )
			{
				$nbComment = get_comments_number( $idPost );
                                    
				$infoComment = '<i class="fa fa-comment"></i> ' . $nbComment . ' '; 
				$infoComment .= ($nbComment>1) ? __('comments', '6tem9Fonction') : __('comment', '6tem9Fonction');

				$tabInfoPost[] = $infoComment;
			}

			if ( $categories && $tabCategoryPost != '' && sizeof($tabCategoryPost) > 0 )
			{
				if ( $iconeCategories )
					$infoCategorie = '<i class="fa fa-folder"></i>&nbsp;';
				else
					$infoCategorie = '';

				$i = 0;
				if ( $activerLienCategories==1 )
				{
					# Archive category link
					foreach ( $tabCategoryPost as $categoryPost )
					{
						if ( $archiveType == 'list' )
	            			$link = get_term_link( $categoryPost );
						else if ( $archiveType == 'gallery' )
							$link = get_post_type_archive_link( get_post($idPost)->post_type ) . '?cat=' . $categoryPost->slug;
						else if ( $archiveType == 'event' )
							$link = get_post_type_archive_link('event') . '?category=' . $categoryPost->slug;
						else if ( $archiveType == 'archive-event' )
							$link = "#";
						else
							$link = "#";
						                        	
       					$infoCategorie .= '<a href="' . $link . '" class="postInfoCategory" data-category="' . $categoryPost->slug . '">' . $categoryPost->name . '</a>';
						
						if ( $i < sizeof($tabCategoryPost)-1 )
							$infoCategorie .= ", "; 
						$i++;                       
    				}	
    			}
    			else
    			{
					// Liste des categories sans liens	
					foreach ( $tabCategoryPost as $categoryPost )
					{						
       					$infoCategorie .= $categoryPost->name;
						if ( $i < sizeof($tabCategoryPost)-1 )
							$infoCategorie .= ", "; 
						$i++;                       
    				}	
				}			
				$tabInfoPost[] = $infoCategorie;
			}

			if ( $tags && $tabKeywordsPost != '' && sizeof($tabKeywordsPost) > 0 )
			{
				if ( $iconeTags )
					$infotags = '<i class="fa fa fa-hashtag"></i>&nbsp;';
				else
					$infotags = '';
           				
				$i = 0;
				foreach ( $tabKeywordsPost as $keywordPost )
				{

					if ( !$public )
						$argLink = '?type=private';
					else
						$argLink = '';

					$infotags .= '<a href="' . get_term_link( $keywordPost, 'tags' ) . $argLink . '" class="postInfoTag">' . $keywordPost->name . '</a>';

					if ( $i < sizeof($tabKeywordsPost)-1 )
						$infotags .= ", ";

					$i++;
				}					
				$tabInfoPost[] = $infotags;
			}				
			
			if ( sizeof($tabElementsSupplementaires)>0 )
				$tabInfoPost = array_merge($tabInfoPost, $tabElementsSupplementaires );
				
		}

		return $before . implode($separateur, $tabInfoPost) . $after;
	}
}


# -----------------------------------------------------------------------------------------------------
# Desactive l'editeur des fichiers des themes et des plugins
if ( !function_exists('fSupprimerEditeurTheme') )
{
	function fSupprimerEditeurTheme()
	{
		define('DISALLOW_FILE_EDIT', TRUE);
	}
	add_action( 'admin_init', 'fSupprimerEditeurTheme' );
}

# -----------------------------------------------------------------------------------------------------
# Remplacement des fichiers d'un plugin
# Exemple d'utilisation :
#    $tabFichier = array(
#        'wp-user-frontend-pro/class/render-form.php' => '6tem9WpufAddon/versions/%s/class/render-form.php'
#    );
#    fUpdateFichier( $tabFichier, 'updatePlugin' );
#    fUpdateFichier( $tabFichier, 'restorePlugin' );
if ( !function_exists( 'fUpdateFichier' ) )
{
    function fUpdateFichier( $tabFichier, $action )
    {
        $dossierPlugins = trailingslashit( dirname( plugin_dir_path(__FILE__) ) );

        foreach ( $tabFichier as $fichierPlugin => $fichierUpdate )
        {
            $fichierUpdate = str_replace( '%s', $action, $fichierUpdate );
            unlink( $dossierPlugins.$fichierPlugin );
            copy ( $dossierPlugins.$fichierUpdate , $dossierPlugins.$fichierPlugin );
        }
    }
}

# -----------------------------------------------------------------------------------------------------
# Definition de variables globales Javascript
/*if ( !function_exists( 'fDeclarationVariableGlobaleJS' ) )
{
	function fDeclarationVariableGlobaleJS()
	{
?>
		<script>
			var globalUrlHome = "<?php echo trailingslashit( get_home_url() ); ?>"; // http://www.urlsite.com/
			var globalUrlPlugins = "<?php echo trailingslashit( plugins_url() ); ?>"; // http://www.urlsite.com/wp-content/plugins/
			var globalUrlThemeParent = "<?php echo trailingslashit( get_template_directory_uri() ); ?>"; // http://www.urlsite.com/wp-content/themes/monTheme/
			var globalUrlTheme = "<?php echo trailingslashit( get_stylesheet_directory_uri() ); ?>"; // http://www.urlsite.com/wp-content/themes/monTheme/
		</script>
<?php
	}
}

add_action( 'wp_head', 'fDeclarationVariableGlobaleJS', 1 );
add_action( 'admin_head', 'fDeclarationVariableGlobaleJS', 1 );*/

# -----------------------------------------------------------------------------------------------------
# Définition des options par défaut du plugin Wordpress SEO en fonction d'un custom post type
if ( !function_exists( 'fSEODefaultOption' ) )
{
	function fSEODefaultOption( $postType )
	{
		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) && get_current_blog_id() != 1 )
		{

			$tabSEOOption = get_option( 'wpseo_titles' );

			# ---------------
			# Titres & Métas
			# Custom post type

			$tabSEOOption['title-'.$postType] = '%%title%% - %%sitename%% %%sep%% %%sitedesc%%';
			$tabSEOOption['metadesc-'.$postType] = '%%excerpt%%';
			$tabSEOOption['hideeditbox-'.$postType] = 'off';
			$tabSEOOption['title-ptarchive-'.$postType] = '%%pt_plural%% %%sep%% Page %%pagenumber%% - %%sitename%% %%sep%% %%sitedesc%%';
			$tabSEOOption['metadesc-ptarchive-'.$postType] = '%%excerpt%%';
	
			# Taxonomies
			$tabSEOOption['title-tax-category-'.$postType] = '%%term_title%% - Page %%pagenumber%% - %%sitename%% %%sep%% %%sitedesc%%';
			$tabSEOOption['hideeditbox-tax-category-'.$postType] = 'on';

			return $tabSEOOption;
		}

	}
}

# -----------------------------------------------------------------------------------------------------
# Suppression des options SEO en fonction d'un custyom post type

if ( !function_exists( 'fSEORemovePostTypeOption' )  )
{
	function fSEORemovePostTypeOption( $postType )
	{
		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) && get_current_blog_id() != 1 )
		{
			$tabSEOOption = get_option( 'wpseo_titles' );

			# Custom post type
			unset($tabSEOOption['title-'.$postType]);
			unset($tabSEOOption['metadesc-'.$postType]);
			unset($tabSEOOption['hideeditbox-'.$postType]);
			unset($tabSEOOption['title-ptarchive-'.$postType]);
			unset($tabSEOOption['metadesc-ptarchive-'.$postType]);
	
			# Taxonomies
			unset($tabSEOOption['title-tax-category-'.$postType]);
			unset($tabSEOOption['hideeditbox-tax-category-'.$postType]);

			return $tabSEOOption;
		}
	}
}

# -----------------------------------------------------------------------------------------------------
# Définition des options par défaut du plugin Wordpress SEO
if ( !function_exists( 'fSEOInitDefaultOption' )  )
{
	function fSEOInitDefaultOption()
	{
		// if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) && get_current_blog_id() != 1 )
		// {

			$tabSEOOption = get_option( 'wpseo_titles' );
			$tabSEOOptionRSS = get_option( 'wpseo_rss' );
			$tabSEOOptionPermalink = get_option( 'wpseo_permalinks' );
			$tabSEOOptionXML = get_option( 'wpseo_xml' );
			$tabSEOOptionSocial = get_option( 'wpseo_social' );

			# -----------------
			# Titres & Métas
			$tabSEOOption['usemetakeywords'] = 1;
			$tabSEOOption['noodp'] = 'on';
			$tabSEOOption['noydir'] = 'on';
			$tabSEOOption['hide-rsdlink'] = 'on';
			$tabSEOOption['hide-wlwmanifest'] = 'on';
			$tabSEOOption['hide-feedlinks'] = 0;
			$tabSEOOption['title-home-wpseo'] = '%%sitename%% %%sep%% %%sitedesc%%';
			$tabSEOOption['title-author-wpseo'] = '%%name%%, Auteur à %%sitename%% %%sep%% %%sitedesc%%';
			$tabSEOOption['metadesc-author-wpseo'] = '%%sitedesc%%';
			$tabSEOOption['noindex-author-wpseo'] = 'on';
			$tabSEOOption['disable-author'] = 'on';
			$tabSEOOption['title-archive-wpseo'] = '%%date%% %%sep%% Page %%pagenumber%% - %%sitename%% %%sep%% %%sitedesc%%';
			$tabSEOOption['metadesc-archive-wpseo'] = '%%sitedesc%%';
			$tabSEOOption['noindex-archive-wpseo'] = 'on';
			$tabSEOOption['title-search-wpseo'] = 'Recherche : %%searchphrase%% - %%sitename%% %%sep%% %%sitedesc%%';
			$tabSEOOption['title-404-wpseo'] = 'Page introuvable - %%sitename%% %%sep%% %%sitedesc%%';

			# -----------------
			# RSS
			$tabSEOOptionRSS['rssafter'] = '%%BLOGLINK%% / %%POSTLINK%%';

			# -----------------
			# Sitemap
			$tabSEOOptionXML['post_types-post-not_in_sitemap'] = 'on';
			$tabSEOOptionXML['post_types-attachment-not_in_sitemap'] = 'on';
			$tabSEOOptionXML['taxonomies-post_format-not_in_sitemap'] = 'on';
			$tabSEOOptionXML['taxonomies-post_tag-not_in_sitemap'] = 'on';
			$tabSEOOptionXML['taxonomies-category-not_in_sitemap'] = 'on';
			$tabSEOOptionXML['post_types-wysijap-not_in_sitemap'] = 'on';

			if ( is_plugin_active( '6tem9Carousel/6tem9Carousel.php' ) )
				$tabSEOOptionXML['post_types-slide-not_in_sitemap'] = 'on';
			if ( is_plugin_active( 'wysija-newsletters/index.php' ) )
				$tabSEOOptionXML['post_types-wysijap-not_in_sitemap'] = 'on';
			if ( is_plugin_active( '6tem9Membership/6tem9Membership.php' ) )
				$tabSEOOptionXML['post_types-privatepage-not_in_sitemap'] = 'on';

			if ( is_plugin_active( '6tem9Event/6tem9Event.php' ) )
			{
				# Post type
				$hasEvents = get_posts('post_type=event');
				$disableSitemapEvent = empty ( $hasEvents ) ? 'on' : 'off';
				$tabSEOOptionXML['post_types-event-not_in_sitemap'] = $disableSitemapEvent;

				# Taxonomies
				$tabTermEvent = get_terms( 'category-event' );
				$disableSitemapTaxonomyEvent = sizeof ( $tabTermEvent ) == 0 ? 'on' : 'off';
				$tabSEOOptionXML['taxonomies-category-event-not_in_sitemap'] = $disableSitemapTaxonomyEvent;

			}

			# -----------------
			# Dynamic post type
			if ( is_plugin_active( '6tem9PostType/6tem9PostType.php' ) )
			{
				# All custom post types
				$tabDynCPT = get_posts( array( 'numberposts' => -1, 'post_type' => 'dyncpt', 'post_status' => 'publish' ) );

				if ( sizeof($tabDynCPT) > 0 )
				{
					$cptSlug = '';

					for ( $i=0; $i<sizeof($tabDynCPT); $i++ )
					{
						# Options for the dynamic CPT
						$idDynCPT = $tabDynCPT[$i]->ID;
						$cptSlug = get_post_meta( $idDynCPT, 'cptSlug', true );

						$hasposts = get_posts('post_type='.$cptSlug);

						if( empty ( $hasposts ) )
							$tabSEOOptionXML['post_types-'.$cptSlug.'-not_in_sitemap'] = 'on';
						else
							$tabSEOOptionXML['post_types-'.$cptSlug.'-not_in_sitemap'] = 'off';

						# Taxonomies
						$tabTermPostType = get_terms( 'category-'.$cptSlug );
						$disableSitemapTaxonomyPostType = sizeof ( $tabTermPostType ) == 0 ? 'on' : 'off';
						$tabSEOOptionXML['taxonomies-category-'.$cptSlug.'-not_in_sitemap'] = $disableSitemapTaxonomyPostType;

					}
				}
			}

			# -----------------
			# Woocommerce
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) )
			{
				# Post type
				$hasProducts = get_posts('post_type=product');
				$disableSitemapProduct = empty ( $hasProducts ) ? 'on' : 'off';
				$tabSEOOptionXML['post_types-product-not_in_sitemap'] = $disableSitemapProduct;

				# Taxonomies
				$tabTermProductCat = get_terms( 'product_cat' );
				$disableSitemapTaxonomyProductCat = sizeof ( $tabTermProductCat ) == 0 ? 'on' : 'off';
				$tabSEOOptionXML['taxonomies-product_cat-not_in_sitemap'] = $disableSitemapTaxonomyProductCat;

				$tabTermProductTag = get_terms( 'product_tag' );
				$disableSitemapTaxonomyProductTag = sizeof ( $tabTermProductTag ) == 0 ? 'on' : 'off';
				$tabSEOOptionXML['taxonomies-product_tag-not_in_sitemap'] = $disableSitemapTaxonomyProductTag;

				$tabTermProductShipping = get_terms( 'product_shipping_class' );
				$disableSitemapTaxonomyProductShipping = sizeof ( $tabTermProductShipping ) == 0 ? 'on' : 'off';
				$tabSEOOptionXML['taxonomies-product_shipping_class-not_in_sitemap'] = $disableSitemapTaxonomyProductShipping;
				
			}

			# -----------------
			# Tags
			$tabTermTag = get_terms( 'tags' );
			$disableSitemapTaxonomyTag = sizeof ( $tabTermTag ) == 0 ? 'on' : 'off';
			$tabSEOOptionXML['taxonomies-tags-not_in_sitemap'] = $disableSitemapTaxonomyTag;

			# ------
			# Social

			$tabSEOOptionSocial['opengraph'] = false;

			# -----------------
			# Permaliens
			$tabSEOOptionPermalink['stripcategorybase'] = 'on';
			$tabSEOOptionPermalink['trailingslash'] = 0;
			$tabSEOOptionPermalink['cleanslugs'] = 0;
			$tabSEOOptionPermalink['redirectattachment'] = 'on';
			$tabSEOOptionPermalink['cleanreplytocom'] = 0;
			$tabSEOOptionPermalink['cleanpermalinks'] = 0;

			# -----------------
			# Custom post types
			$args = array( 'public'   => true );
			$output = 'names';
			$tabPostType = get_post_types( $args, $output ); 

			if ( sizeof($tabPostType) > 0 )
			{
				foreach( $tabPostType as $postType )
				{
					$tabSEOOption['title-'.$postType] = '%%title%% - %%sitename%% %%sep%% %%sitedesc%%';
					$tabSEOOption['metadesc-'.$postType] = '%%excerpt%%';
					$tabSEOOption['hideeditbox-' . $postType] = '';

					$tabSEOOption['title-ptarchive-'.$postType] = '%%pt_plural%% %%sep%% Page %%pagenumber%% - %%sitename%% %%sep%% %%sitedesc%%';
					$tabSEOOption['metadesc-ptarchive-'.$postType] = '%%excerpt%%';
				}
			}

			# -----------------
			# Taxonomies
			$tabTaxonomy = get_taxonomies( array( 'public' => true ), 'objects' );

			if ( is_array( $tabTaxonomy ) && $tabTaxonomy !== array() )
			{
				foreach ( $tabTaxonomy as $taxonomy ) 
				{
					$taxonomyPostType = get_post_type_object($taxonomy->object_type[0]);
					$tabSEOOption['title-tax-'.$taxonomy->name] = $taxonomyPostType->labels->name . ' %%term_title%% - %%sitename%% %%sep%% %%sitedesc%% - Page %%pagenumber%%';
					$tabSEOOption['hideeditbox-tax-'.$taxonomy->name] = 'on';
				}
			}

			update_option( 'wpseo_titles', $tabSEOOption );
			update_option( 'wpseo_rss', $tabSEOOptionRSS );
			update_option( 'wpseo_permalinks', $tabSEOOptionPermalink );
			update_option( 'wpseo_xml', $tabSEOOptionXML );
			update_option( 'wpseo_social', $tabSEOOptionSocial );
		// }
	}

	// if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) && get_current_blog_id() != 1 )
	// {
	// 	add_action( 'delete_post', 'fSEOInitDefaultOption' );
	// 	add_action( 'save_post', 'fSEOInitDefaultOption' );
	// }
}

# -----------------------------------------------------------------------------------------------------
# Ordonne la liste des sites d'un réseau dans l'ordre alphabétique
if ( !function_exists( 'fAlphaOrderMySites' )  )
{
    function fAlphaOrderMySites()
    {
        global $wp_admin_bar;
        
        $tabBlogName = array();
        $tabSite = $wp_admin_bar->user->blogs;
// n_print( $tabSite);
        foreach ( $tabSite as $siteId => $site )
            $tabBlogName[$siteId] = strtoupper($site->domain);
        
        # Remove main blog from list...we want that to show at the top
        unset($tabBlogName[1]);
        
        # Order by name
        asort($tabBlogName);
        
        # Create new array
        $wp_admin_bar->user->blogs = array();
        
        # Add main blog back in to list
        if ( isset($tabSite[1]) && is_user_member_of_blog( get_current_user_id(), 1 ) )
        	$wp_admin_bar->user->blogs{1} = $tabSite[1];
        
        # Add others back in alphabetically
        foreach ( $tabBlogName as $siteId => $name )
            $wp_admin_bar->user->blogs{$siteId} = $tabSite[$siteId];
    }

    if ( is_multisite() )
		add_action('admin_bar_menu', 'fAlphaOrderMySites');
}

// if ( !function_exists( 'fAlphaOrderMySites' )  )
// {
//     function fAlphaOrderMySites()
//     {
//         global $wp_admin_bar;
        
//         $tabBlogName = array();
//         $tabSite = $wp_admin_bar->user->blogs;

//         foreach ( $tabSite as $siteId => $site )
//             $tabBlogName[$siteId] = strtoupper($site->blogname);
        
//         # Remove main blog from list...we want that to show at the top
//         unset($tabBlogName[1]);
        
//         # Order by name
//         asort($tabBlogName);
        
//         # Create new array
//         $wp_admin_bar->user->blogs = array();
        
//         # Add main blog back in to list
//         if ( isset($tabSite[1]) && is_user_member_of_blog( get_current_user_id(), 1 ) )
//         	$wp_admin_bar->user->blogs{1} = $tabSite[1];
        
//         # Add others back in alphabetically
//         foreach ( $tabBlogName as $siteId=>$name )
//             $wp_admin_bar->user->blogs{$siteId} = $tabSite[$siteId];
//     }

//     if ( is_multisite() )
// 		add_action('admin_bar_menu', 'fAlphaOrderMySites');
// }

# -----------------------------------------------------------------------------------------------------
# Ajoute une barre de recherche à la liste des sites d'un réseau
if ( !function_exists( 'fSearchBarMySites' )  )
{
    function fSearchBarMySites( $wp_admin_bar )
    {
        
        if( ! is_user_logged_in() )
		return;

		$wp_admin_bar->add_menu( array(
			'parent' => 'my-sites-list',
			'id'     => 'my-sites-search',
			'title'  => '<label for="my-sites-search-text">'. __( 'Filter My Sites', '6tem9Fonction' ) .'</label>' .
						'<input type="text" id="my-sites-search-text" placeholder="'. __( 'Search', '6tem9Fonction' ) .'" />',
			'meta'   => array(
				'class' => 'hide-if-no-js'
			)
		) );
    }

    if ( is_multisite() )
		add_action('admin_bar_menu', 'fSearchBarMySites');
}

# -----------------------------------------------------------------------------------------------------
# Scripts et styles de la barre de recherche
if ( !function_exists( 'fSearchBarMySitesStyleScript' )  )
{
	function fSearchBarMySitesStyleScript( )
	{
		if ( ! is_admin_bar_showing() || !is_user_logged_in() )
			return;

		wp_enqueue_script( 'networkSiteSearch', plugins_url( 'js/my-sites-search.js', dirname( __FILE__ ) ), array('jquery'), '2014.07.30', true );
		wp_enqueue_style( 'networkSiteSearch', plugins_url( 'css/my-sites-search.css', dirname( __FILE__ ) ) );
	}

	if ( is_multisite() )
	{
		add_action( 'wp_enqueue_scripts', 'fSearchBarMySitesStyleScript' );
		add_action( 'admin_enqueue_scripts', 'fSearchBarMySitesStyleScript' );
	}
}

# -----------------------------------------------------------------------------------------------------
# Get image (logo) id by url
# Used for show the format of the logo image
if ( !function_exists( 'fGetAttachmentIdByUrl' )  )
{
	function fGetAttachmentIdByUrl( $url ) 
	{

        $dir = wp_upload_dir();

        // baseurl never has a trailing slash
        if ( false === strpos( $url, $dir['baseurl'] . '/' ) ) 
            return false; // URL points to a place outside of upload directory

        $file  = basename( $url );
        $query = array(
            'post_type'  => 'attachment',
            'fields'     => 'ids',
            'meta_query' => array(
                array(
                    'value'   => $file,
                    'compare' => 'LIKE',
                ),
            )
        );

        $query['meta_query'][0]['key'] = '_wp_attached_file';

        // query attachments
        $ids = get_posts( $query );

        if ( ! empty( $ids ) ) 
        {
            foreach ( $ids as $id ) 
            {
                // first entry of returned array is the URL
                $urlAttachmentImageSrc = wp_get_attachment_image_src( $id, 'full' );
                if ( $url === array_shift( $urlAttachmentImageSrc ) )
                    return $id;
            }
        }

        $query['meta_query'][0]['key'] = '_wp_attachment_metadata';

        // query attachments again
        $ids = get_posts( $query );

        if ( empty($ids) )
            return false;

        foreach ( $ids as $id ) 
        {
            $meta = wp_get_attachment_metadata( $id );

            foreach ( $meta['sizes'] as $size => $values ) 
            {
                if ( $values['file'] === $file && $url === array_shift( wp_get_attachment_image_src( $id, $size ) ) )
                    return $id;
            }
        }

        return false;
    }
}


# -----------------------------------------------------------------------------------------------------
# Get a menu ID by its name
if ( !function_exists( 'fGetMenuIdByName' )  )
{
	function fGetMenuIdByName( $name ) 
	{
	    $tabMenu = get_terms( 'nav_menu' ); 

	    foreach ( $tabMenu as $menu ) 
	        if( $name === $menu->name )
	            return $menu->term_id;
	    return false;
	}
}


# -----------------------------------------------------------------------------------------------------
function fDoRobots() 
{
 	header( 'Content-Type: text/plain; charset=utf-8' );

	/**
	* Fires when displaying the robots.txt file.
	*
	* @since 2.1.0
	*/
	do_action( 'do_robotstxt' );

	$output .= "Allow: /*.js$ \n";
	$output .= "Allow: /*.css$ \n";
	$output .= 'Sitemap: ' . get_bloginfo('url') . "/sitemap_index.xml";

	/**
	* Filter the robots.txt output.
	*
	* @since 3.0.0
	*
	* @param string $output Robots.txt output.
	* @param bool   $public Whether the site is considered "public".
	*/
	echo apply_filters( 'robots_txt', $output, $public );
}

add_action('do_robots', 'fDoRobots');

# -----------------------------------------------------------------------------------------------------
# Lost password : User stay on his website, he is not directed to the portal

# -----------------------------------------------------------------------------------------------------
# Fixes "Lost Password?" URLs on login page
function fRedirectLostPasswordUrl( $url, $redirect )
{
	$args = array( 'action' => 'lostpassword' );
    
    if ( !empty($redirect) )
        $args['redirect_to'] = $redirect;

    return add_query_arg( $args, site_url('wp-login.php') );
}

add_filter("lostpassword_url", 'fRedirectLostPasswordUrl', 10, 2);

# -----------------------------------------------------------------------------------------------------
# Fixes other password reset related urls
function fRedirectNetworkSiteUrlLostPassword( $url, $path, $scheme )
{
	if (stripos($url, "action=lostpassword") !== false)
        return site_url('wp-login.php?action=lostpassword', $scheme);
  
    if (stripos($url, "action=resetpass") !== false)
        return site_url('wp-login.php?action=resetpass', $scheme);
  
    return $url;
}

add_filter( 'network_site_url', 'fRedirectNetworkSiteUrlLostPassword', 10, 3 );

# -----------------------------------------------------------------------------------------------------
# Fixes URLs in email that goes out
function fRetrievePasswordMessageRedirect( $message, $key ) 
{
	return str_replace(get_site_url(1), get_site_url(), $message);
}
add_filter("retrieve_password_message", 'fRetrievePasswordMessageRedirect', 10, 2);

# -----------------------------------------------------------------------------------------------------
# Remove "get shortlink" on admin page
add_filter( 'pre_get_shortlink', '__return_empty_string' );

# -----------------------------------------------------------------------------------------------------
# Remove all help tabs on admin
function fRemoveAllHelpTabs( $oldHelp, $screenId, $screen )
{
    $screen->remove_help_tabs();
    return $oldHelp;
}

add_filter( 'contextual_help', 'fRemoveAllHelpTabs', 999, 3 );

# -----------------------------------------------------------------------------------------------------
# Gravity forms

if ( fPluginIsActivate( 'gravityforms/gravityforms.php' ) )
{

	function fGravityFormsLicenceKey( $value )
	{
		if( defined('GF_LICENSE_KEY') )
			return GF_LICENSE_KEY;
		else
			return $value;
	}

	add_filter( 'pre_option_rg_gforms_key', 'fGravityFormsLicenceKey' );
}

# -----------------------------------------------------------------------------------------------------
# Simple flush rules

function fFlushRules()
{
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

# -----------------------------------------------------------------------------------------------------
# Get terms depending on custom post types
function fGetTermsByPostTypes( $taxonomies, $postTypes, $args ) 
{
    global $wpdb;

    $number = isset($args['number']) ? $args['number'] : 20;
    $orderBy = isset($args['orderBy']) ? $args['orderBy'] : 'count';
    $order = isset($args['order']) ? $args['order'] : 'DESC';
    if ( $orderBy == 'manual' )
    	$orderBy = 't.term_order';
    
    $query = stripslashes($wpdb->prepare(
        "SELECT t.*, r.term_taxonomy_id, tt.taxonomy, tt.description, tt.parent, COUNT(*) AS count from $wpdb->terms AS t
        INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
        INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
        WHERE p.post_type IN('%s') AND tt.taxonomy IN('%s')
        GROUP BY t.term_id
        ORDER BY " . $orderBy . " " . $order . "
        LIMIT %u",
        join( "', '", $postTypes ),
        join( "', '", $taxonomies ),
        $number
    ));

    $results = $wpdb->get_results( $query );

    return $results;

}
# -----------------------------------------------------------------------------------------------------
# Transform hexa color to rgba

function fHexaToRGBA( $color, $opacity )
{
	$default = 'rgb(0,0,0)';
 
	# Return default if no color provided
	if ( empty($color) )
          return $default; 
 
	# Sanitize $color if "#" is provided 
    if ( $color[0] == '#' )
    	$color = substr( $color, 1 );

    # Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6)
        $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
    else if ( strlen( $color ) == 3 )
        $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
    else
        return $default;

    # Convert hexadec to rgb
    $rgb = array_map('hexdec', $hex);

    # Check if opacity is set(rgba or rgb)
	if( abs($opacity) > 1 )
		$opacity = 1.0;
	$output = 'rgba(' . implode(",",$rgb) . ',' . $opacity.')';

    # Return rgb(a) color string
    return $output;
}

# -----------------------------------------------------------------------------------------------------
# Get <img> tag from attachment ID
# Parameters : Array > attachment ID, image format, alt, class, style

function fGetAttachmentHtmlTag( $tabArgs )
{
	$showImageTag = false;

	# If required attributes are set
	if ( isset($tabArgs['idAttachment']) && $tabArgs['idAttachment'] != '' && isset($tabArgs['format']) )
	{
		$urlAttachment = wp_get_attachment_image_src( $tabArgs['idAttachment'], $tabArgs['format'] ); // Src, width and height
		$srcsetAttachment = wp_get_attachment_image_srcset( $tabArgs['idAttachment'], $tabArgs['format'] ); // SrcSet
		$sizesAttachment = wp_get_attachment_image_sizes( $tabArgs['idAttachment'], $tabArgs['format'] ); // Sizes

		$attributesImage = 'width="' . $urlAttachment[1] . '" height="' . $urlAttachment[2] . '" src="' . $urlAttachment[0] . '"';
		
		if ( $srcsetAttachment != '' )
			$attributesImage .= ' srcset="' . $srcsetAttachment . '"';

		if ( $sizesAttachment != '' )
			 $attributesImage .= ' sizes="' . $sizesAttachment . '"';

		$showImageTag = true;
	}

	# If the required attributes have been set, we can add the optionnal attributes
	if ( $showImageTag )
	{
		# Class
		if ( isset($tabArgs['class']) && $tabArgs['class'] != '' )
			$attributesImage .= ' class="' . $tabArgs['class'] . '"';

		# Alt
		if ( isset($tabArgs['alt']) && $tabArgs['alt'] != '' )
			$attributesImage .= ' alt="' . $tabArgs['alt'] . '"';

		# Style
		if ( isset($tabArgs['style']) && $tabArgs['style'] != '' )
			$attributesImage .= ' style="' . $tabArgs['style'] . '"';

		# Data
		if ( isset($tabArgs['data']) && sizeof($tabArgs['data']) > 0 )
		{
			foreach( $tabArgs['data'] as $dataKey => $data )
				$attributesImage .= ' ' . $dataKey . '="' .  $data . '"';
		}

		$galleryPostThumbnail = '<img ' . $attributesImage . '  />';
	}
	
	return $showImageTag ? $galleryPostThumbnail : '';
}


# -----------------------------------------------------------------------------------------------------
if  ( !function_exists('fIpClient') )
{
	function fIpClient() 
	{
	    $ipaddress = '';
	    if ( getenv('HTTP_CLIENT_IP') )
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if ( getenv('HTTP_X_FORWARDED_FOR') )
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if ( getenv('HTTP_X_FORWARDED') )
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if ( getenv('HTTP_FORWARDED_FOR') )
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if ( getenv('HTTP_FORWARDED') )
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if ( getenv('REMOTE_ADDR') )
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}
}

# -----------------------------------------------------------------------------------------------------
// Return $filename mime_type 
// Source : http://www.iana.org/assignments/media-types/media-types.xhtml
if ( !function_exists('fMimeContentType') ) 
{
    function fMimeContentType( $filepath ) 
    {
		$filename = basename($filepath);
    	if ( function_exists('finfo_open') ) 
        {
			$debutCheminServeur = $_SERVER['DOCUMENT_ROOT'];
			$finCheminFichier =  strstr($filepath, "/wp-content/");
			$cheminCompletFichier = $debutCheminServeur.$finCheminFichier;

            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $cheminCompletFichier);
            finfo_close($finfo);
            $mimetype = explode(';', $mimetype)[0];
            return $mimetype;
        }
        else 
        {
            return 'application/octet-stream';
        }
    }
}


/**
 * Retourne une chaine formatee : utile pour numero de facture, de bon de commande etc
 *
 * @since    1.0.0
 * @param  string   $string   Chaîne de caractère à formater
 * @param  array    $tabArg 
 */
function fChaineFormatee( $string, $tabArg=array() ) 
{
    $tabArgDefaut = array(
            'prefixe' => '', 
            'suffixe' => '',
            'longueur' => 10, 
            'remplirAvec' => '0', 
		);

    $tabArgFinal = array_replace( $tabArgDefaut, $tabArg );
    $longueurstring = strlen($string);
    $nombreCaractere = $tabArgFinal['longueur'] - $longueurstring;
    
    for ( $i=0; $i<$nombreCaractere-1; $i++ )
        $tabArgFinal['prefixe'] .= $tabArgFinal['remplirAvec'];
        
    return $tabArgFinal['prefixe'] . $string . $tabArgFinal['suffixe']; 
}

/**
 * Check if an e-mail address is blacklisted
 * WARNING : need "require_once('../../../plugins/cleantalk-spam-protect/inc/cleantalk.class.php');" when called
 * @param  string $email the e-mail address to check
 */

if ( !function_exists('fIsEmailBlacklisted') ) 
{
    function fIsEmailBlacklisted( $email ) 
    {
    	session_start();

		$isBlacklisted = false;

		if ( is_plugin_active('cleantalk-spam-protect/cleantalk.php') && $email != '' )
		{
		    $data = Array();
		    $data[] = $_SERVER['REMOTE_ADDR']; // Fake authorized IP // Prod : Client IP
		    $data[] = sanitize_email($email);

		    $data = implode(',',$data);

		    $request = Array();
		    $request['data'] = $data; 

		    $ct_options = ct_get_options();
		    $url = 'https://api.cleantalk.org/?method_name=spam_check_cms&auth_key=' . $ct_options['apikey'];

		    $result = sendRawRequest($url, $request, false, 5);
		    $result = json_decode($result);

		    $checkEmail = sanitize_email($email);
		    
		    foreach( $result->data as $key => $objAppears )
		    {
		        if ( $key == sanitize_email($email) )
		          $isBlacklisted = $objAppears->appears == 1;
		    }
		}
		else
		  	$isBlacklisted = false;

		return $isBlacklisted;
    }
}
# -----------------------------------------------------------------------------------------------------
# Remove sites from "My sites" where the current user can't access the back office

// function fRemoveNotAdminSite($blogs) 
// {
// 	global $current_user; 

// 	if( !is_super_admin() && $current_user->ID != '2' )
// 	{
// 		foreach ( $blogs as $blogId => $blog )
// 		{
// 		    # Remove this blog from the list if the user can't access admin
// 		    if ( !current_user_can_for_blog($blogId, '6tem9_can_access_wp-admin') )
// 		        unset( $blogs[ $blogId ] );
// 		}
// 	}

// 	return $blogs;
// }    

// add_filter( 'get_blogs_of_user', 'fRemoveNotAdminSite' );

/**
 *
 * @since 1.6.4
 * @param string   $array
 * @param array    $key 
 * @param array    $data 
 */
function fInsertBeforeKey( $array, $key, $data = null )
{
    if ( ($offset = array_search($key, array_keys($array))) === false ) // if the key doesn't exist
    {
        $offset = 0; // should we prepend $array with $data?
        $offset = count($array); // or should we append $array with $data? lets pick this one...
    }

    return array_merge(array_slice($array, 0, $offset), (array) $data, array_slice($array, $offset));
}

/**
 * Retourne le tableau des bots autorisés à parser le site
 *
 * @since 1.6.4
 */
function fListBotSearchEngine()
{
	$tab = array();
	$tab[] = 'googlebot';
	$tab[] = 'googlebot-image';
	$tab[] = 'googlebot-video';
	$tab[] = 'googlebot-news';
	$tab[] = 'adsbot-google';
	$tab[] = 'bingbot';
	$tab[] = 'duckduckbot';
	$tab[] = 'sogou';
	$tab[] = 'baiduspider';
	$tab[] = 'baidu web search	';
	$tab[] = 'baidu image search	';
	$tab[] = 'baiduspider-image';
	$tab[] = 'baidu mobile search	';
	$tab[] = 'baiduspider-mobile';
	$tab[] = 'baidu video search	';
	$tab[] = 'baiduspider-video';
	$tab[] = 'baidu news search	';
	$tab[] = 'baiduspider-news';
	$tab[] = 'baidu bookmark search	';
	$tab[] = 'baiduspider-favo';
	$tab[] = 'baidu union search	';
	$tab[] = 'baiduspider-cpro';
	$tab[] = 'baidu business search	';
	$tab[] = 'baiduspider-ads';
	$tab[] = 'slurp';
	$tab[] = 'yandexbot';
	$tab[] = 'yandexaccessibilitybot';
	$tab[] = 'yandexmobilebot';
	$tab[] = 'yandexdirectdyn';
	$tab[] = 'yandeximages';
	$tab[] = 'yandexvideo';
	$tab[] = 'yandexmedia';
	$tab[] = 'yandexblogs';
	$tab[] = 'yandexfavicons';
	$tab[] = 'yandexwebmaster';
	$tab[] = 'yandexpagechecker';
	$tab[] = 'yandeximageresizer';
	$tab[] = 'yandexcalendar';
	$tab[] = 'yandexsitelinks';
	$tab[] = 'yandexmetrika';
	$tab[] = 'yandexantivirus';
	$tab[] = 'yandexvertis';
	$tab[] = 'yandexbot';
	$tab[] = 'mediapartners';

	$i = 0;
	// while ( $i<sizeof($tab) && $return==false )
	while ( $i<sizeof($tab) )
	{
		$return = strstr(strtolower($_SERVER['HTTP_USER_AGENT']), $tab[$i] ); 
		$i++;
	}

	return $return;
}


function table_type_mime( $extension )
{
	$type_myme = array(
		'3dm' => 'x-world/x-3dmf',
		'3dmf' => 'x-world/x-3dmf',
		'a' => 'application/octet-stream',
		'aab' => 'application/x-authorware-bin',
		'aam' => 'application/x-authorware-map',
		'aas' => 'application/x-authorware-seg',
		'abc' => 'text/vnd.abc',
		'acgi' => 'text/html',
		'afl' => 'video/animaflex',
		'ai' => 'application/postscript',
		'aif' => 'audio/aiff',
		'aif' => 'audio/x-aiff',
		'aifc' => 'audio/aiff',
		'aifc' => 'audio/x-aiff',
		'aiff' => 'audio/aiff',
		'aiff' => 'audio/x-aiff',
		'aim' => 'application/x-aim',
		'aip' => 'text/x-audiosoft-intra',
		'ani' => 'application/x-navi-animation',
		'aos' => 'application/x-nokia-9000-communicator-add-on-software',
		'aps' => 'application/mime',
		'arc' => 'application/octet-stream',
		'arj' => 'application/arj',
		'arj' => 'application/octet-stream',
		'art' => 'image/x-jg',
		'asf' => 'video/x-ms-asf',
		'asm' => 'text/x-asm',
		'asp' => 'text/asp',
		'asx' => 'application/x-mplayer2',
		'asx' => 'video/x-ms-asf',
		'asx' => 'video/x-ms-asf-plugin',
		'au' => 'audio/basic',
		'au' => 'audio/x-au',
		'avi' => 'application/x-troff-msvideo',
		'avi' => 'video/avi',
		'avi' => 'video/msvideo',
		'avi' => 'video/x-msvideo',
		'avs' => 'video/avs-video',
		'bcpio' => 'application/x-bcpio',
		'bin' => 'application/mac-binary',
		'bin' => 'application/macbinary',
		'bin' => 'application/octet-stream',
		'bin' => 'application/x-binary',
		'bin' => 'application/x-macbinary',
		'bm' => 'image/bmp',
		'bmp' => 'image/bmp',
		'bmp' => 'image/x-windows-bmp',
		'boo' => 'application/book',
		'book' => 'application/book',
		'boz' => 'application/x-bzip2',
		'bsh' => 'application/x-bsh',
		'bz' => 'application/x-bzip',
		'bz2' => 'application/x-bzip2',
		'c' => 'text/plain',
		'c' => 'text/x-c',
		'c' => 'text/plain',
		'cat' => 'application/vnd.ms-pki.seccat',
		'cc' => 'text/plain',
		'cc' => 'text/x-c',
		'ccad' => 'application/clariscad',
		'cco' => 'application/x-cocoa',
		'cdf' => 'application/cdf',
		'cdf' => 'application/x-cdf',
		'cdf' => 'application/x-netcdf',
		'cer' => 'application/pkix-cert',
		'cer' => 'application/x-x509-ca-cert',
		'cha' => 'application/x-chat',
		'chat' => 'application/x-chat',
		'class' => 'application/java',
		'class' => 'application/java-byte-code',
		'class' => 'application/x-java-class',
		'com' => 'application/octet-stream',
		'com' => 'text/plain',
		'conf' => 'text/plain',
		'cpio' => 'application/x-cpio',
		'cpp' => 'text/x-c',
		'cpt' => 'application/mac-compactpro',
		'cpt' => 'application/x-compactpro',
		'cpt' => 'application/x-cpt',
		'crl' => 'application/pkcs-crl',
		'crl' => 'application/pkix-crl',
		'crt' => 'application/pkix-cert',
		'crt' => 'application/x-x509-ca-cert',
		'crt' => 'application/x-x509-user-cert',
		'csh' => 'application/x-csh',
		'csh' => 'text/x-script.csh',
		'css' => 'application/x-pointplus',
		'css' => 'text/css',
		'cxx' => 'text/plain',
		'dcr' => 'application/x-director',
		'deepv' => 'application/x-deepv',
		'def' => 'text/plain',
		'der' => 'application/x-x509-ca-cert',
		'dif' => 'video/x-dv',
		'dir' => 'application/x-director',
		'dl' => 'video/dl',
		'dl' => 'video/x-dl',
		'doc' => 'application/msword',
		'dot' => 'application/msword',
		'dp' => 'application/commonground',
		'drw' => 'application/drafting',
		'dump' => 'application/octet-stream',
		'dv' => 'video/x-dv',
		'dvi' => 'application/x-dvi',
		'dwf' => 'drawing/x-dwf (old)',
		'dwf' => 'model/vnd.dwf',
		'dwg' => 'application/acad',
		'dwg' => 'image/vnd.dwg',
		'dwg' => 'image/x-dwg',
		'dxf' => 'application/dxf',
		'dxf' => 'image/vnd.dwg',
		'dxf' => 'image/x-dwg',
		'dxr' => 'application/x-director',
		'el' => 'text/x-script.elisp',
		'elc' => 'application/x-bytecode.elisp (compiled elisp)',
		'elc' => 'application/x-elc',
		'env' => 'application/x-envoy',
		'eps' => 'application/postscript',
		'es' => 'application/x-esrehber',
		'etx' => 'text/x-setext',
		'evy' => 'application/envoy',
		'evy' => 'application/x-envoy',
		'exe' => 'application/octet-stream',
		'f' => 'text/plain',
		'f' => 'text/x-fortran',
		'f77' => 'text/x-fortran',
		'f90' => 'text/plain',
		'f90' => 'text/x-fortran',
		'fdf' => 'application/vnd.fdf',
		'fif' => 'application/fractals',
		'fif' => 'image/fif',
		'fli' => 'video/fli',
		'fli' => 'video/x-fli',
		'flo' => 'image/florian',
		'flx' => 'text/vnd.fmi.flexstor',
		'fmf' => 'video/x-atomic3d-feature',
		'for' => 'text/plain',
		'for' => 'text/x-fortran',
		'fpx' => 'image/vnd.fpx',
		'fpx' => 'image/vnd.net-fpx',
		'frl' => 'application/freeloader',
		'funk' => 'audio/make',
		'g' => 'text/plain',
		'g3' => 'image/g3fax',
		'gif' => 'image/gif',
		'gl' => 'video/gl',
		'gl' => 'video/x-gl',
		'gsd' => 'audio/x-gsm',
		'gsm' => 'audio/x-gsm',
		'gsp' => 'application/x-gsp',
		'gss' => 'application/x-gss',
		'gtar' => 'application/x-gtar',
		'gz' => 'application/x-compressed',
		'gz' => 'application/x-gzip',
		'gzip' => 'application/x-gzip',
		'gzip' => 'multipart/x-gzip',
		'h' => 'text/plain',
		'h' => 'text/x-h',
		'hdf' => 'application/x-hdf',
		'help' => 'application/x-helpfile',
		'hgl' => 'application/vnd.hp-hpgl',
		'hh' => 'text/plain',
		'hh' => 'text/x-h',
		'hlb' => 'text/x-script',
		'hlp' => 'application/hlp',
		'hlp' => 'application/x-helpfile',
		'hlp' => 'application/x-winhelp',
		'hpg' => 'application/vnd.hp-hpgl',
		'hpgl' => 'application/vnd.hp-hpgl',
		'hqx' => 'application/binhex',
		'hqx' => 'application/binhex4',
		'hqx' => 'application/mac-binhex',
		'hqx' => 'application/mac-binhex40',
		'hqx' => 'application/x-binhex40',
		'hqx' => 'application/x-mac-binhex40',
		'hta' => 'application/hta',
		'htc' => 'text/x-component',
		'htm' => 'text/html',
		'html' => 'text/html',
		'htmls' => 'text/html',
		'htt' => 'text/webviewhtml',
		'htx' => 'text/html',
		'ice' => 'x-conference/x-cooltalk',
		'ico' => 'image/x-icon',
		'idc' => 'text/plain',
		'ief' => 'image/ief',
		'iefs' => 'image/ief',
		'iges' => 'application/iges',
		'iges' => 'model/iges',
		'igs' => 'application/iges',
		'igs' => 'model/iges',
		'ima' => 'application/x-ima',
		'imap' => 'application/x-httpd-imap',
		'inf' => 'application/inf',
		'ins' => 'application/x-internett-signup',
		'ip' => 'application/x-ip2',
		'isu' => 'video/x-isvideo',
		'it' => 'audio/it',
		'iv' => 'application/x-inventor',
		'ivr' => 'i-world/i-vrml',
		'ivy' => 'application/x-livescreen',
		'jam' => 'audio/x-jam',
		'jav' => 'text/plain',
		'jav' => 'text/x-java-source',
		'java' => 'text/plain',
		'java' => 'text/x-java-source',
		'jcm' => 'application/x-java-commerce',
		'jfif' => 'image/jpeg',
		'jfif' => 'image/pjpeg',
		'jfif' => '73e47 d8073e50 ">image/jpeg',
		'jpe' => 'image/jpeg',
		'jpe' => 'image/pjpeg',
		'jpeg' => 'image/jpeg',
		'jpeg' => 'image/pjpeg',
		'jpg' => 'image/jpeg',
		'jpg' => 'image/pjpeg',
		'jps' => 'image/x-jps',
		'js' => 'application/x-javascript',
		'js' => 'application/javascript',
		'js' => 'application/ecmascript',
		'js' => 'text/javascript',
		'js' => 'text/ecmascript',
		'jut' => 'image/jutvision',
		'kar' => 'audio/midi',
		'kar' => 'music/x-karaoke',
		'ksh' => 'application/x-ksh',
		'ksh' => 'text/x-script.ksh',
		'la' => 'audio/nspaudio',
		'la' => 'audio/x-nspaudio',
		'lam' => 'audio/x-liveaudio',
		'latex' => 'application/x-latex',
		'lha' => 'application/lha',
		'lha' => 'application/octet-stream',
		'lha' => 'application/x-lha',
		'lhx' => 'application/octet-stream',
		'list' => 'text/plain',
		'lma' => 'audio/nspaudio',
		'lma' => 'audio/x-nspaudio',
		'log' => 'text/plain',
		'lsp' => 'application/x-lisp',
		'lsp' => 'text/x-script.lisp',
		'lst' => 'text/plain',
		'lsx' => 'text/x-la-asf',
		'ltx' => 'application/x-latex',
		'lzh' => 'application/octet-stream',
		'lzh' => 'application/x-lzh',
		'lzx' => 'application/lzx',
		'lzx' => 'application/octet-stream',
		'lzx' => 'application/x-lzx',
		'm' => 'text/plain',
		'm' => 'text/x-m',
		'm1v' => 'video/mpeg',
		'm2a' => 'audio/mpeg',
		'm2v' => 'video/mpeg',
		'm3u' => 'audio/x-mpequrl',
		'man' => 'application/x-troff-man',
		'map' => 'application/x-navimap',
		'mar' => 'text/plain',
		'mbd' => 'application/mbedlet',
		'mc' => 'application/x-magic-cap-package-1.0',
		'mcd' => 'application/mcad',
		'mcd' => 'application/x-mathcad',
		'mcf' => 'image/vasa',
		'mcf' => 'text/mcf',
		'mcp' => 'application/netmc',
		'me' => 'application/x-troff-me',
		'mht' => 'message/rfc822',
		'mhtml' => 'message/rfc822',
		'mid' => 'application/x-midi',
		'mid' => 'audio/midi',
		'mid' => 'audio/x-mid',
		'mid' => 'audio/x-midi',
		'mid' => 'music/crescendo',
		'mid' => 'x-music/x-midi',
		'midi' => 'application/x-midi',
		'midi' => 'audio/midi',
		'midi' => 'audio/x-mid',
		'midi' => 'audio/x-midi',
		'midi' => 'music/crescendo',
		'midi' => 'x-music/x-midi',
		'mif' => 'application/x-frame',
		'mif' => 'application/x-mif',
		'mime' => 'message/rfc822',
		'mime' => 'www/mime',
		'mjf' => 'audio/x-vnd.audioexplosion.mjuicemediafile',
		'mjpg' => 'video/x-motion-jpeg',
		'mm' => 'application/base64',
		'mm' => 'application/x-meme',
		'mme' => 'application/base64',
		'mod' => 'audio/mod',
		'mod' => 'audio/x-mod',
		'moov' => 'video/quicktime',
		'mov' => 'video/quicktime',
		'movie' => 'video/x-sgi-movie',
		'mp2' => 'audio/mpeg',
		'mp2' => 'audio/x-mpeg',
		'mp2' => 'video/mpeg',
		'mp2' => 'video/x-mpeg',
		'mp2' => 'video/x-mpeq2a',
		'mp3' => 'audio/mpeg3',
		'mp3' => 'audio/x-mpeg-3',
		'mp3' => 'video/mpeg',
		'mp3' => 'video/x-mpeg',
		'mpa' => 'audio/mpeg',
		'mpa' => 'video/mpeg',
		'mpc' => 'application/x-project',
		'mpe' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'mpg' => 'audio/mpeg',
		'mpg' => 'video/mpeg',
		'mpga' => 'audio/mpeg',
		'mpp' => 'application/vnd.ms-project',
		'mpt' => 'application/x-project',
		'mpv' => 'application/x-project',
		'mpx' => 'application/x-project',
		'mrc' => 'application/marc',
		'ms' => 'application/x-troff-ms',
		'mv' => 'video/x-sgi-movie',
		'my' => 'audio/make',
		'mzz' => 'application/x-vnd.audioexplosion.mzz',
		'nap' => 'image/naplps',
		'naplps' => 'image/naplps',
		'nc' => 'application/x-netcdf',
		'ncm' => 'application/vnd.nokia.configuration-message',
		'nif' => 'image/x-niff',
		'niff' => 'image/x-niff',
		'nix' => 'application/x-mix-transfer',
		'nsc' => 'application/x-conference',
		'nvd' => 'application/x-navidoc',
		'o' => 'application/octet-stream',
		'oda' => 'application/oda',
		'omc' => 'application/x-omc',
		'omcd' => 'application/x-omcdatamaker',
		'omcr' => 'application/x-omcregerator',
		'p' => 'text/x-pascal',
		'p10' => 'application/pkcs10',
		'p10' => 'application/x-pkcs10',
		'p12' => 'application/pkcs-12',
		'p12' => 'application/x-pkcs12',
		'p7a' => 'application/x-pkcs7-signature',
		'p7c' => 'application/pkcs7-mime',
		'p7c' => 'application/x-pkcs7-mime',
		'p7m' => 'application/pkcs7-mime',
		'p7m' => 'application/x-pkcs7-mime',
		'p7r' => 'application/x-pkcs7-certreqresp',
		'p7s' => 'application/pkcs7-signature',
		'part' => 'application/pro_eng',
		'pas' => 'text/pascal',
		'pbm' => 'image/x-portable-bitmap',
		'pcl' => 'application/vnd.hp-pcl',
		'pcl' => 'application/x-pcl',
		'pct' => 'image/x-pict',
		'pcx' => 'image/x-pcx',
		'pdb' => 'chemical/x-pdb',
		'pdf' => 'application/pdf',
		'pfunk' => 'audio/make',
		'pfunk' => 'audio/make.my.funk',
		'pgm' => 'image/x-portable-graymap',
		'pgm' => 'image/x-portable-greymap',
		'pic' => 'image/pict',
		'pict' => 'image/pict',
		'pkg' => 'application/x-newton-compatible-pkg',
		'pko' => 'application/vnd.ms-pki.pko',
		'pl' => 'text/plain',
		'pl' => 'text/x-script.perl',
		'plx' => 'application/x-pixclscript',
		'pm' => 'image/x-xpixmap',
		'pm' => 'text/x-script.perl-module',
		'pm4' => 'application/x-pagemaker',
		'pm5' => 'application/x-pagemaker',
		'png' => 'image/png',
		'pnm' => 'application/x-portable-anymap',
		'pnm' => 'image/x-portable-anymap',
		'pot' => 'application/mspowerpoint',
		'pot' => 'application/vnd.ms-powerpoint',
		'pov' => 'model/x-pov',
		'ppa' => 'application/vnd.ms-powerpoint',
		'ppm' => 'image/x-portable-pixmap',
		'pps' => 'application/mspowerpoint',
		'pps' => 'application/vnd.ms-powerpoint',
		'ppt' => 'application/mspowerpoint',
		'ppt' => 'application/powerpoint',
		'ppt' => 'application/vnd.ms-powerpoint',
		'ppt' => 'application/x-mspowerpoint',
		'ppz' => 'application/mspowerpoint',
		'pre' => 'application/x-freelance',
		'prt' => 'application/pro_eng',
		'ps' => 'application/postscript',
		'psd' => 'application/octet-stream',
		'pvu' => 'paleovu/x-pv',
		'pwz' => 'application/vnd.ms-powerpoint',
		'py' => 'text/x-script.phyton',
		'pyc' => 'application/x-bytecode.python',
		'qcp' => 'audio/vnd.qcelp',
		'qd3' => 'x-world/x-3dmf',
		'qd3d' => 'x-world/x-3dmf',
		'qif' => 'image/x-quicktime',
		'qt' => 'video/quicktime',
		'qtc' => 'video/x-qtc',
		'qti' => 'image/x-quicktime',
		'qtif' => 'image/x-quicktime',
		'ra' => 'audio/x-pn-realaudio',
		'ra' => 'audio/x-pn-realaudio-plugin',
		'ra' => 'audio/x-realaudio',
		'ram' => 'audio/x-pn-realaudio',
		'ras' => 'application/x-cmu-raster',
		'ras' => 'image/cmu-raster',
		'ras' => 'image/x-cmu-raster',
		'rast' => 'image/cmu-raster',
		'rexx' => 'text/x-script.rexx',
		'rf' => 'image/vnd.rn-realflash',
		'rgb' => 'image/x-rgb',
		'rm' => 'application/vnd.rn-realmedia',
		'rm' => 'audio/x-pn-realaudio',
		'rmi' => 'audio/mid',
		'rmm' => 'audio/x-pn-realaudio',
		'rmp' => 'audio/x-pn-realaudio',
		'rmp' => 'audio/x-pn-realaudio-plugin',
		'rng' => 'application/ringing-tones',
		'rng' => 'application/vnd.nokia.ringing-tone',
		'rnx' => 'application/vnd.rn-realplayer',
		'roff' => 'application/x-troff',
		'rp' => 'image/vnd.rn-realpix',
		'rpm' => 'audio/x-pn-realaudio-plugin',
		'rt' => 'text/richtext',
		'rt' => 'text/vnd.rn-realtext',
		'rtf' => 'application/rtf',
		'rtf' => 'application/x-rtf',
		'rtf' => 'text/richtext',
		'rtx' => 'application/rtf',
		'rtx' => 'text/richtext',
		'rv' => 'video/vnd.rn-realvideo',
		's' => 'text/x-asm',
		's3m' => 'audio/s3m',
		'saveme' => 'application/octet-stream',
		'sbk' => 'application/x-tbook',
		'scm' => 'application/x-lotusscreencam',
		'scm' => 'text/x-script.guile',
		'scm' => 'text/x-script.scheme',
		'scm' => 'video/x-scm',
		'sdml' => 'text/plain',
		'sdp' => 'application/sdp',
		'sdp' => 'application/x-sdp',
		'sdr' => 'application/sounder',
		'sea' => 'application/sea',
		'sea' => 'application/x-sea',
		'set' => 'application/set',
		'sgm' => 'text/sgml',
		'sgm' => 'text/x-sgml',
		'sgml' => 'text/sgml',
		'sgml' => 'text/x-sgml',
		'sh' => 'application/x-bsh',
		'sh' => 'application/x-sh',
		'sh' => 'application/x-shar',
		'sh' => 'text/x-script.sh',
		'shar' => 'application/x-bsh',
		'shar' => 'application/x-shar',
		'shtml' => 'text/html',
		'shtml' => 'text/x-server-parsed-html',
		'sid' => 'audio/x-psid',
		'sit' => 'application/x-sit',
		'sit' => 'application/x-stuffit',
		'skd' => 'application/x-koan',
		'skm' => 'application/x-koan',
		'skp' => 'application/x-koan',
		'skt' => 'application/x-koan',
		'sl' => 'application/x-seelogo',
		'smi' => 'application/smil',
		'smil' => 'application/smil',
		'snd' => 'audio/basic',
		'snd' => 'audio/x-adpcm',
		'sol' => 'application/solids',
		'spc' => 'application/x-pkcs7-certificates',
		'spc' => 'text/x-speech',
		'spl' => 'application/futuresplash',
		'spr' => 'application/x-sprite',
		'sprite' => 'application/x-sprite',
		'src' => 'application/x-wais-source',
		'ssi' => 'text/x-server-parsed-html',
		'ssm' => 'application/streamingmedia',
		'sst' => 'application/vnd.ms-pki.certstore',
		'step' => 'application/step',
		'stl' => 'application/sla',
		'stl' => 'application/vnd.ms-pki.stl',
		'stl' => 'application/x-navistyle',
		'stp' => 'application/step',
		'sv4cpio' => 'application/x-sv4cpio',
		'sv4crc' => 'application/x-sv4crc',
		'svf' => 'image/vnd.dwg',
		'svf' => 'image/x-dwg',
		'svr' => 'application/x-world',
		'svr' => 'x-world/x-svr',
		'swf' => 'application/x-shockwave-flash',
		't' => 'application/x-troff',
		'talk' => 'text/x-speech',
		'tar' => 'application/x-tar',
		'tbk' => 'application/toolbook',
		'tbk' => 'application/x-tbook',
		'tcl' => 'application/x-tcl',
		'tcl' => 'text/x-script.tcl',
		'tcsh' => 'text/x-script.tcsh',
		'tex' => 'application/x-tex',
		'texi' => 'application/x-texinfo',
		'texinfo' => 'application/x-texinfo',
		'text' => 'application/plain',
		'text' => 'text/plain',
		'tgz' => 'application/gnutar',
		'tgz' => 'application/x-compressed',
		'tif' => 'image/tiff',
		'tif' => 'image/x-tiff',
		'tiff' => 'image/tiff',
		'tiff' => 'image/x-tiff',
		'tr' => 'application/x-troff',
		'tsi' => 'audio/tsp-audio',
		'tsp' => 'application/dsptype',
		'tsp' => 'audio/tsplayer',
		'tsv' => 'text/tab-separated-values',
		'turbot' => 'image/florian',
		'txt' => 'text/plain',
		'uil' => 'text/x-uil',
		'uni' => 'text/uri-list',
		'unis' => 'text/uri-list',
		'unv' => 'application/i-deas',
		'uri' => 'text/uri-list',
		'uris' => 'text/uri-list',
		'ustar' => 'application/x-ustar',
		'ustar' => 'multipart/x-ustar',
		'uu' => 'application/octet-stream',
		'uu' => 'text/x-uuencode',
		'uue' => 'text/x-uuencode',
		'vcd' => 'application/x-cdlink',
		'vcs' => 'text/x-vcalendar',
		'vda' => 'application/vda',
		'vdo' => 'video/vdo',
		'vew' => 'application/groupwise',
		'viv' => 'video/vivo',
		'viv' => 'video/vnd.vivo',
		'vivo' => 'video/vivo',
		'vivo' => 'video/vnd.vivo',
		'vmd' => 'application/vocaltec-media-desc',
		'vmf' => 'application/vocaltec-media-file',
		'voc' => 'audio/voc',
		'voc' => 'audio/x-voc',
		'vos' => 'video/vosaic',
		'vox' => 'audio/voxware',
		'vqe' => 'audio/x-twinvq-plugin',
		'vqf' => 'audio/x-twinvq',
		'vql' => 'audio/x-twinvq-plugin',
		'vrml' => 'application/x-vrml',
		'vrml' => 'model/vrml',
		'vrml' => 'x-world/x-vrml',
		'vrt' => 'x-world/x-vrt',
		'vsd' => 'application/x-visio',
		'vst' => 'application/x-visio',
		'vsw' => 'application/x-visio',
		'w60' => 'application/wordperfect6.0',
		'w61' => 'application/wordperfect6.1',
		'w6w' => 'application/msword',
		'wav' => 'audio/wav',
		'wav' => 'audio/x-wav',
		'wb1' => 'application/x-qpro',
		'wbmp' => 'image/vnd.wap.wbmp',
		'web' => 'application/vnd.xara',
		'wiz' => 'application/msword',
		'wk1' => 'application/x-123',
		'wmf' => 'windows/metafile',
		'wml' => 'text/vnd.wap.wml',
		'wmlc' => 'application/vnd.wap.wmlc',
		'wmls' => 'text/vnd.wap.wmlscript',
		'wmlsc' => 'application/vnd.wap.wmlscriptc',
		'word' => 'application/msword',
		'wp' => 'application/wordperfect',
		'wp5' => 'application/wordperfect',
		'wp5' => 'application/wordperfect6.0',
		'wp6' => 'application/wordperfect',
		'wpd' => 'application/wordperfect',
		'wpd' => 'application/x-wpwin',
		'wq1' => 'application/x-lotus',
		'wri' => 'application/mswrite',
		'wri' => 'application/x-wri',
		'wrl' => 'application/x-world',
		'wrl' => 'model/vrml',
		'wrl' => 'x-world/x-vrml',
		'wrz' => 'model/vrml',
		'wrz' => 'x-world/x-vrml',
		'wsc' => 'text/scriplet',
		'wsrc' => 'application/x-wais-source',
		'wtk' => 'application/x-wintalk',
		'xbm' => 'image/x-xbitmap',
		'xbm' => 'image/x-xbm',
		'xbm' => 'image/xbm',
		'xdr' => 'video/x-amt-demorun',
		'xgz' => 'xgl/drawing',
		'xif' => 'image/vnd.xiff',
		'xl' => 'application/excel',
		'xla' => 'application/excel',
		'xla' => 'application/x-excel',
		'xla' => 'application/x-msexcel',
		'xlb' => 'application/excel',
		'xlb' => 'application/vnd.ms-excel',
		'xlb' => 'application/x-excel',
		'xlc' => 'application/excel',
		'xlc' => 'application/vnd.ms-excel',
		'xlc' => 'application/x-excel',
		'xld' => 'application/excel',
		'xld' => 'application/x-excel',
		'xlk' => 'application/excel',
		'xlk' => 'application/x-excel',
		'xll' => 'application/excel',
		'xll' => 'application/vnd.ms-excel',
		'xll' => 'application/x-excel',
		'xlm' => 'application/excel',
		'xlm' => 'application/vnd.ms-excel',
		'xlm' => 'application/x-excel',
		'xls' => 'application/excel',
		'xls' => 'application/vnd.ms-excel',
		'xls' => 'application/x-excel',
		'xls' => 'application/x-msexcel',
		'xlt' => 'application/excel',
		'xlt' => 'application/x-excel',
		'xlv' => 'application/excel',
		'xlv' => 'application/x-excel',
		'xlw' => 'application/excel',
		'xlw' => 'application/vnd.ms-excel',
		'xlw' => 'application/x-excel',
		'xlw' => 'application/x-msexcel',
		'xm' => 'audio/xm',
		'xml' => 'application/xml',
		'xml' => 'text/xml',
		'xmz' => 'xgl/movie',
		'xpix' => 'application/x-vnd.ls-xpix',
		'xpm' => 'image/x-xpixmap',
		'xpm' => 'image/xpm',
		'x' => '73e47 d8073e50 ">image/png',
		'xsr' => 'video/x-amt-showrun',
		'xwd' => 'image/x-xwd',
		'xwd' => 'image/x-xwindowdump',
		'xyz' => 'chemical/x-pdb',
		'z' => 'application/x-compress',
		'z' => 'application/x-compressed',
		'zip' => 'application/x-compressed',
		'zip' => 'application/x-zip-compressed',
		'zip' => 'application/zip',
		'zip' => 'multipart/x-zip',
		'zoo' => 'application/octet-stream',
		'zsh' => 'text/x-script.zsh',
	);	
	
	// if ( array_key_exists($extension, $type_myme) )
		return $type_myme[$extension];
	// else
		// return false;
}
?>