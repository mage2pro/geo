<?php
use Df\Geo\Client as C;
use Geocoder\Model\Address as A;
use Geocoder\Model\AdminLevelCollection as ALs;
/**
 * 2017-04-24
 * @used-by \Df\Geo\Test\Basic::t01()
 * @used-by \Dfe\Moip\P\Charge::pAddress()
 * @used-by \Dfe\Moip\Test\Data::ga()
 */
function df_geo(string $k, string $l = '', string $region = ''):C {return dfcf(
	function(string $k, string $l, string $region) {return new C($k, $l, $region);}
	,[$k, df_locale($l), $region]
);}

/**
 * 2017-04-24
 * Google Maps API Reference:
 * «administrative_area_level_2 indicates a second-order civil entity below the country level.
 * Within the United States, these administrative levels are counties.
 * Not all nations exhibit these administrative levels.»
 * https://developers.google.com/maps/documentation/geocoding/intro#Types
 * @used-by \Dfe\Moip\P\Charge::pAddress()
 * @used-by \Dfe\Moip\Test\Data::address()
 */
function df_geo_city(A $a):string {
	$l = $a->getAdminLevels(); /** @var ALs $l */
	return 1 < $l->count() ? $l->get(1)->getName() : '';
}

/**
 * 2017-04-24
 * Google Maps API Reference:
 * «administrative_area_level_1 indicates a first-order civil entity below the country level.
 * Within the United States, these administrative levels are states.
 * Not all nations exhibit these administrative levels.
 * In most cases, administrative_area_level_1 short names will closely match ISO 3166-2 subdivisions
 * and other widely circulated lists; however this is not guaranteed
 * as our geocoding results are based on a variety of signals and location data.»
 * https://developers.google.com/maps/documentation/geocoding/intro#Types
 * @used-by \Dfe\Moip\P\Charge::pAddress()
 * @used-by \Dfe\Moip\Test\Data::address()
 */
function df_geo_state_code(A $a):string {
	$l = $a->getAdminLevels(); /** @var ALs $l */
	return $l->count() ? $l->first()->getCode() : '';
}