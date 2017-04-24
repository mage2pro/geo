<?php
namespace Df\Geo\T;
use Geocoder\Model\Address as A;
use Geocoder\Model\AddressCollection as AA;
use Geocoder\Provider\GoogleMaps as API;
use Ivory\HttpAdapter\CurlHttpAdapter as Adapter;
use Ivory\HttpAdapter\HttpAdapterInterface as IAdapter;
// 2017-04-24
final class Basic extends TestCase {
	/** @test 2017-04-24 */
	function t01() {
		/**
		 * 2017-04-24
		 * @param IAdapter $adapter An HTTP adapter
		 * @param string $locale  A locale (optional)
		 * @param string $region  Region biasing (optional)
		 * @param bool $useSsl  Whether to use an SSL connection (optional)
		 * @param string $apiKey  Google Geocoding API key (optional)
		 * @var API $api
		 */
		$api = new API(new Adapter
			,null
			,null
			// 2017-04-24
			// PHPDoc: «Whether to use an SSL connection (optional)».
			// Google Maps требует значение true.
			,true
			// 2017-04-24
			// «Your application's API key.
			// This key identifies your application for purposes of quota management.»
			// https://developers.google.com/maps/documentation/geocoding/intro#geocoding
			// «How to generate a key for the Google Maps Geocoding API?» https://mage2.pro/t/3828
			,'AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc'
		);
		// 2017-04-24
		// «The street address that you want to geocode,
		// in the format used by the national postal service of the country concerned.
		// Additional address elements such as business names and unit, suite or floor numbers
		// should be avoided. Please refer to the FAQ for additional guidance.»
		// https://developers.google.com/maps/documentation/geocoding/intro#geocoding
		/** @var AA $aa */
		$aa = $api->geocode('Av. Lúcio Costa, 3150 - Barra da Tijuca, Rio de Janeiro - RJ, 22630-010');
		/**
		 * 2017-04-24
		 * Обращение к несуществующим элементам приводит к исключительной ситуации:
		 * @see \Geocoder\Model\AddressCollection::first()
		 * @see \Geocoder\Model\AddressCollection::get()
		 */
		if (count($aa)) {
			/** @var A $a */
			$a = $aa->first();
			echo df_dump([
				// 2017-04-24 In my case: «BR».
				'country' => $a->getCountryCode()
				// 2017-04-24 In my case: «null».
				,'locality' => $a->getLocality()
				// 2017-04-24 In my case: «22630-010».
				,'postalCode' => $a->getPostalCode()
				// 2017-04-24 In my case: «Avenida Lúcio Costa».
				,'streetName' => $a->getStreetName()
				// 2017-04-24 In my case: «3150».
				,'streetNumber' => $a->getStreetNumber()
			]);
		}
		$a = $aa->first();
		xdebug_break();
	}
}