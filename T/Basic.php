<?php
namespace Df\Geo\T;
use Geocoder\Model\AddressCollection as Addresses;
use Geocoder\Provider\GoogleMaps as API;
use Ivory\HttpAdapter\CurlHttpAdapter as Adapter;
// 2017-04-24
final class Basic extends TestCase {
	/** @test 2017-04-24 */
	function t01() {
		/** @var API $api */
		$api = new API(new Adapter, null, null, true, 'AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc');
		/** @var Addresses $aa */
		$aa = $api->geocode('Av. Lúcio Costa, 3150 - Barra da Tijuca, Rio de Janeiro - RJ, 22630-010');
		xdebug_break();
	}
}