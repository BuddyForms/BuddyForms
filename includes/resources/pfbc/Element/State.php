<?php

/**
 * Class Element_State
 */
class Element_State extends Element_Select {
	/**
	 * Element_State constructor.
	 *
	 * @param $label
	 * @param $name
	 * @param array|null $properties
	 */
	public function __construct( $label, $name, array $properties = null ) {
		$options = array(
			""        => "--Select State--",
			"nostate" => "No State",
			"AL"      => "Alabama",
			"AK"      => "Alaska",
			"AZ"      => "Arizona",
			"AR"      => "Arkansas",
			"CA"      => "California",
			"CO"      => "Colorado",
			"CT"      => "Connecticut",
			"DE"      => "Delaware",
			"DC"      => "District of Columbia",
			"FL"      => "Florida",
			"GA"      => "Georgia",
			"HI"      => "Hawaii",
			"ID"      => "Idaho",
			"IL"      => "Illinois",
			"IN"      => "Indiana",
			"IA"      => "Iowa",
			"KS"      => "Kansas",
			"KY"      => "Kentucky",
			"LA"      => "Louisiana",
			"ME"      => "Maine",
			"MD"      => "Maryland",
			"MA"      => "Massachusetts",
			"MI"      => "Michigan",
			"MN"      => "Minnesota",
			"MS"      => "Mississippi",
			"MO"      => "Missouri",
			"MT"      => "Montana",
			"NE"      => "Nebraska",
			"NV"      => "Nevada",
			"NH"      => "New Hampshire",
			"NJ"      => "New Jersey",
			"NM"      => "New Mexico",
			"NY"      => "New York",
			"NC"      => "North Carolina",
			"ND"      => "North Dakota",
			"OH"      => "Ohio",
			"OK"      => "Oklahoma",
			"OR"      => "Oregon",
			"PA"      => "Pennsylvania",
			"RI"      => "Rhode Island",
			"SC"      => "South Carolina",
			"SD"      => "South Dakota",
			"TN"      => "Tennessee",
			"TX"      => "Texas",
			"UT"      => "Utah",
			"VT"      => "Vermont",
			"VA"      => "Virginia",
			"WA"      => "Washington",
			"WV"      => "West Virginia",
			"WI"      => "Wisconsin",
			"WY"      => "Wyoming"
		);
		if ( buddyforms_core_fs()->is_plan( 'professional' ) || buddyforms_core_fs()->is_trial() ) {
			$options = apply_filters( 'buddyforms_state_values', $options, $properties );
		}
		parent::__construct( $label, $name, $options, $properties );
	}
}
