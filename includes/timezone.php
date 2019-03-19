<?php
include_once( plugin_dir_path( __FILE__ ) . 'countries.php');

function nc_timezone_search_timezone() {
	if( wp_doing_ajax() ){
		$search = $_GET['nc_place_search'];
		$result = nc_timezone_send_request($search);
		$output = nc_timezone_format_result($result);

		if( !$output ){
			//let's try again
			sleep(3);
			$result = nc_timezone_send_request($search);
			$output = nc_timezone_format_result($result);

			if( !$output ){
				echo json_encode( array( 'status' => 'FAILED', 'place' => array($result) ) );

			} else{
				echo json_encode( array( 'status' => 'OK', 'result' => $output ) );
			}
		}
		else{
			echo json_encode( array( 'status' => 'OK', 'result' => $output ) );
		}
	}
	die();
}



add_action( 'wp_ajax_nc_timezone_search_timezone', 'nc_timezone_search_timezone' );
add_action( 'wp_ajax_nopriv_nc_timezone_search_timezone', 'nc_timezone_search_timezone' );

function nc_timezone_send_request($search){
	$settings = get_option( 'nc_timezone_settings' );
	$search_array = explode(",", $search);
	$city = $search_array[0];
	$country_code = nc_timezone_get_country_code( $search_array[ sizeof($search_array) - 1 ] );
	$country_code = empty($country_code) ? 'US' : $country_code;

	$lat_lang = nc_timezone_get_lat_lang($search);
	$timezone = nc_timezone_get_timezone($lat_lang['lat_lang']);

	$url = $settings['nc_timezone_api_gateway'] . '/v2/get-time-zone?key=' . $settings['nc_timezone_api_key'] . '&format=json&by=zone&city=' . urlencode($city) . '&country=' . urlencode($country_code) . '&zone=' . urlencode($timezone['timezone']);
	$result = file_get_contents($url);
	return array('place' => $search, 'result' => $result, 'request_url' => $url, 'timezone' => $timezone, 'lat_lang' => $lat_lang);
}


function nc_timezone_format_result($result){
	$zone = json_decode($result['result']);
	if($zone->status !== "OK"){
		return false;
	} 
 	ob_start();
	?>
	<div class="nc-result-row" data-actual-date="<?php echo $zone->timestamp;?>" data-id="<?php echo time();?>" data-timezone=<?php echo date_default_timezone_get();?>>
		<div class="nc-tzplace">
				<div class="nc-label">Place</div>
				<strong><?php echo $result['place'];?></strong> 
		</div>

		<div class="nc-tzzone">
				<div class="nc-label">Zone</div>
				<strong><?php echo $zone->zoneName;?></strong> 
		</div>

		<div class="nc-tztime">
			<div class="nc-label">Time</div>
			<span class="time"><strong class="hour"><?php echo date('h', $zone->timestamp);?></strong><i class="sep">:</i><strong class="minute"><?php echo date('i', $zone->timestamp);?></strong><strong class="seconds"><?php echo date('s', $zone->timestamp);?></strong><strong class="ante"><?php echo date('A', $zone->timestamp);?></strong></span>
		</div>

		<div class="nc-tzdate">
			<div class="nc-label">Date</div>
			<strong><span class="fulldate"><?php echo date('D, M d Y', $zone->timestamp);?> </span></strong>
		</div>

		<div class="nc-tzrmv">
			<button class="btnremove">X</button>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php $output = ob_get_clean(); return $output;
}

function nc_timezone_get_country_code($country_name){
	$countries = nc_timezone_get_countries();
    $code = array_search( strtolower(trim($country_name)), array_map('strtolower', $countries) ); 
 	return $code;
}

function nc_timezone_get_lat_lang($address){
	$url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) ."&sensor=false";
	$result = json_decode( file_get_contents($url), true );


	return array('request_url' => $url, 'lat_lang' => $result['results'][0]['geometry']['location']);
	
}

function nc_timezone_get_timezone($location){
	//set AIzaSyASFlS80O92QORSr7TAfWVzLERtunGDvzw to admin
	$settings = get_option( 'nc_timezone_settings' );
	$url = "https://maps.googleapis.com/maps/api/timezone/json?location=" . $location['lat'] . ',' . $location['lng'] ."&timestamp=" . time() ."&key=" . $settings['nc_timezone_google_timezone_api_key'];
	$result = json_decode( file_get_contents($url), true );
	return array('request_url' => $url, 'timezone' => $result['timeZoneId']);
}


function nc_timezone_get_countries(){
	return array
	(
		'AF' => 'Afghanistan',
		'AX' => 'Aland Islands',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua And Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia And Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei Darussalam',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CG' => 'Congo',
		'CD' => 'Congo, Democratic Republic',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => 'Cote D\'Ivoire',
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands (Malvinas)',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard Island & Mcdonald Islands',
		'VA' => 'Holy See (Vatican City State)',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran, Islamic Republic Of',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IM' => 'Isle Of Man',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KR' => 'Korea',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Lao People\'s Democratic Republic',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libyan Arab Jamahiriya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macao',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia, Federated States Of',
		'MD' => 'Moldova',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestinian Territory, Occupied',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russian Federation',
		'RW' => 'Rwanda',
		'BL' => 'Saint Barthelemy',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts And Nevis',
		'LC' => 'Saint Lucia',
		'MF' => 'Saint Martin',
		'PM' => 'Saint Pierre And Miquelon',
		'VC' => 'Saint Vincent And Grenadines',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome And Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia And Sandwich Isl.',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard And Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad And Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks And Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'GB' => 'United Kingdom',
		'US' => 'United States',
		'UM' => 'United States Outlying Islands',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VE' => 'Venezuela',
		'VN' => 'Viet Nam',
		'VG' => 'Virgin Islands, British',
		'VI' => 'Virgin Islands, U.S.',
		'WF' => 'Wallis And Futuna',
		'EH' => 'Western Sahara',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	);
}


?>