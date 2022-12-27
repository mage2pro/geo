<?php
namespace Df\Geo\Test;
use Geocoder\Model\Address as A;
use Geocoder\Model\AddressCollection as AA;
# 2017-04-24
final class Basic extends CaseT {
	/** 2017-04-24 */
	function t01():void {
		# 2017-04-24
		# Google Maps API Reference: «The street address that you want to geocode,
		# in the format used by the national postal service of the country concerned.
		# Additional address elements such as business names and unit, suite or floor numbers
		# should be avoided. Please refer to the FAQ for additional guidance.»
		# https://developers.google.com/maps/documentation/geocoding/intro#geocoding
		/** @var AA $aa */
		$aa = df_geo(self::$K)->all('Av. Lúcio Costa, 3150 - Barra da Tijuca, Rio de Janeiro - RJ, 22630-010');
		/**
		 * 2017-04-24
		 * Обращение к несуществующим элементам приводит к исключительной ситуации:
		 * @see \Geocoder\Model\AddressCollection::first()
		 * @see \Geocoder\Model\AddressCollection::get()
		 */
		if (count($aa)) {
			/** @var A $a */
			$a = $aa->first();
			xdebug_break();
			# Google Maps API Reference:
			# https://developers.google.com/maps/documentation/geocoding/intro#Types
			print_r(df_dump([
				'city' => $a->getAdminLevels()->first()->getName()
				# 2017-04-24
				# In my case: «BR».
				# Google Maps API Reference:
				# «Indicates the national political entity,
				# and is typically the highest order type returned by the Geocoder.»
				,'country' => $a->getCountryCode()
				# 2017-04-24
				# In my case: «null».
				# Google Maps API Reference: «Indicates an incorporated city or town political entity.»
				,'locality' => $a->getLocality()
				# 2017-04-24
				# In my case: «22630-010».
				# Google Maps API Reference:
				# «Indicates a postal code as used to address postal mail within the country.»
				,'postalCode' => $a->getPostalCode()
				/**
				 * 2017-04-24
				 * In my case: «Avenida Lúcio Costa».
				 * Google Maps API Reference: «Indicates a named route (such as "US 101").»
				 * Google Maps API это значение передаёт в поле «route»:
				 * @see \Geocoder\Provider\GoogleMaps::updateAddressComponent()
				 */
				,'streetName' => $a->getStreetName()
				# 2017-04-24 In my case: «3150».
				# Google Maps API Reference: «Indicates the precise street number.»
				,'street_number' => $a->getStreetNumber()
				# 2017-04-24 In my case: «Barra da Tijuca».
				# Google Maps API Reference:
				# «Indicates a first-order civil entity below a locality.
				# For some locations may receive one of the additional types:
				# sublocality_level_1 to sublocality_level_5.
				# Each sublocality level is a civil entity.
				# Larger numbers indicate a smaller geographic area.».
				,'sublocality' => $a->getSubLocality()
			]));
		}
	}
	
	/** 2017-07-20 @test */
	function t02():void {
		/** @var A $a */
		$a = df_geo(self::$K, 'pt-BR', 'br')->p('fasfesedf Rio de Janeiro 201111111');
		print_r(df_json_encode($a->toArray()));
	}

	/**
	 * 2017-07-20
	 * @const
	 * @used-by t01()
	 * @used-by t02()
	 */
	private static $K = 'AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc';
}