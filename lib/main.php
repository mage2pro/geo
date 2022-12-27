<?php
use Df\Geo\Client as C;
use Geocoder\Model\Address as A;
use Geocoder\Model\AdminLevelCollection as ALs;
/**
 * 2017-04-24
 * @used-by \Df\Geo\Test\Basic::t01()
 * @used-by \Dfe\Moip\P\Charge::pAddress()
 * @used-by \Dfe\Moip\Test\Data::ga()
 * @param string|null $locale [optional]
 * @param string|null $tld [optional]
 * @return C
 */
function df_geo(string $key, $locale = null, $tld = null) {return C::s($key, $locale, $tld);}

/**
 * 2017-04-24
 * Google Maps API Reference:
 * «administrative_area_level_2 indicates a second-order civil entity below the country level.
 * Within the United States, these administrative levels are counties.
 * Not all nations exhibit these administrative levels.»
 * https://developers.google.com/maps/documentation/geocoding/intro#Types
 * @used-by \Dfe\Moip\P\Reg::pShippingAddress()
 * @param A $a
 * @return string|null
 */
function df_geo_city(A $a) {
	/** @var ALs $als */
	$als = $a->getAdminLevels();
	return 1 < $als->count() ? $als->get(1)->getName() : null;
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
 * @used-by \Dfe\Moip\P\Reg::pShippingAddress()
 * @param A $a
 * @return string|null
 */
function df_geo_state_code(A $a) {
	/** @var ALs $als */
	$als = $a->getAdminLevels();
	return $als->count() ? $als->first()->getCode() : null;
}