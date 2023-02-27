<?php
namespace Dfe\Geo;
use Closure as F;
use Df\Core\Exception as DFE;
use Exception as E;
use Geocoder\Model\Address as GA;
use Geocoder\Model\AddressCollection as AA;
use Geocoder\Provider\GoogleMaps\GoogleMaps as Provider;
use Geocoder\StatefulGeocoder as API;
use Magento\Sales\Model\Order\Address as A;
# 2017-07-20
final class Client {
	/**
	 * 2017-07-20
	 * @used-by df_geo()
	 */
	function __construct(string $key, string $locale, string $region = '') {
		# 2022-10-26 https://github.com/geocoder-php/Geocoder/tree/4.3.0#usage
		$this->_api = new API(
			new Provider(
				# 2022-10-26 https://stackoverflow.com/a/33134596
				new \GuzzleHttp\Client(['curl' => [CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2]])
				/**
				 * 2017-04-24
				 * PHPDoc: «Region biasing (optional)».
				 *
				 * Google Maps API Reference:
				 * «The region code, specified as a ccTLD ("top-level domain") two-character value.
				 * This parameter will only influence, not fully restrict, results from the geocoder.»
				 * https://developers.google.com/maps/documentation/geocoding/intro#geocoding
				 *
				 * «Region Biasing
				 * In a geocoding request, you can instruct the Geocoding service
				 * to return results biased to a particular region by using the region parameter.
				 * This parameter takes a ccTLD (country code top-level domain) argument
				 * specifying the region bias.
				 * Most ccTLD codes are identical to ISO 3166-1 codes, with some notable exceptions.
				 *
				 * For example, the United Kingdom's ccTLD is "uk" (.co.uk) while its ISO 3166-1 code is "gb"
				 * (technically for the entity of "The United Kingdom of Great Britain and Northern Ireland").
				 * Geocoding results can be biased for every domain
				 * in which the main Google Maps application is officially launched.
				 * Note that biasing only prefers results for a specific domain;
				 * if more relevant results exist outside of this domain, they may be included.
				 *
				 * For example, a geocode for "Toledo" returns this result,
				 * as the default domain for the Google Maps Geocoding API is set to the United States.
				 * Request: https://maps.googleapis.com/maps/api/geocode/json?address=Toledo&key=YOUR_API_KEY
				 * Response:
				 *	{
				 *		"results": [
				 *			{<...>, "formatted_address": "Toledo, OH, USA", <...>},
				 *			{<...>, "formatted_address": "Toledo, OR, USA", <...>},
				 *			{<...>, "formatted_address": "Toledo, IA, USA", <...>},
				 *			{<...>, "formatted_address": "Toledo, WA 98591, USA", <...>}
				 *		],
				 *		"status": "OK"
				 *	}
				 * A geocoding request for "Toledo" with region=es (Spain) will return the Spanish city.
				 * Request: https://maps.googleapis.com/maps/api/geocode/json?address=Toledo&region=es&key=YOUR_API_KEY
				 * Response:
				 *	{
				 *		"results": [
				 *			{<...>, "formatted_address": "Toledo, Toledo, Spain", <...>}
				 *		],
				 *		"status": "OK"
				 *	}
				 * https://developers.google.com/maps/documentation/geocoding/intro#RegionCodes
				 * »
				 *
				 * Мой пример:
				 * *) https://maps.googleapis.com/maps/api/geocode/json?region=ru&address=Petersburg&key=AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc
				 * Response: «St Petersburg, Russia»
				 * *) https://maps.googleapis.com/maps/api/geocode/json?region=us&address=Petersburg&key=AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc
				 * Response: «Petersburg, VA, USA»
				 *
				 * Но вообще API достаточно умён и по дополнительным параметрам адреса определяет страну.
				 *
				 * *) https://maps.googleapis.com/maps/api/geocode/json?region=co.uk&address=Birmingham&key=AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc
				 * Response: «Birmingham, UK»
				 * *) https://maps.googleapis.com/maps/api/geocode/json?region=us&address=Birmingham&key=AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc
				 * Response: «Birmingham, UK»
				 * https://maps.googleapis.com/maps/api/geocode/json?region=us&address=Birmingham+Alabama&key=AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc
				 * Response: «Birmingham, AL, USA»
				 *
				 * Так что, думаю, можно пока обойтись без этого параметра.
				 *
				 * 2022-12-17
				 * I use @see df_etn() because of:
				 * @see \Geocoder\Provider\GoogleMaps\GoogleMaps::buildQuery():
				 *		if (null !== $region) {
				 *			$url = sprintf('%s&region=%s', $url, $region);
				 *		}
				 * https://github.com/geocoder-php/Geocoder/blob/4.3.0/src/Provider/GoogleMaps/GoogleMaps.php#L179-L181
				 */
				,df_etn($region)
				# 2017-04-24
				# Google Maps API Reference: «Your application's API key.
				# This key identifies your application for purposes of quota management.»
				# https://developers.google.com/maps/documentation/geocoding/intro#geocoding
				# «How to generate a key for the Google Maps Geocoding API?» https://mage2.pro/t/3828
				,$key
			)
			/**
			 * 2017-04-24
			 * PHPDoc: «A locale (optional)».
			 * Google Maps API Reference:
			 * «language — The language in which to return results.
			 * See the list of supported languages: https://developers.google.com/maps/faq#languagesupport
			 * Google often updates the supported languages, so this list may not be exhaustive.
			 * If language is not supplied,
			 * the geocoder attempts to use the preferred language as specified in the Accept-Language header,
			 * or the native language of the domain from which the request is sent.
			 * The geocoder does its best to provide a street address
			 * that is readable for both the user and locals.
			 * To achieve that goal, it returns street addresses in the local language,
			 * transliterated to a script readable by the user if necessary, observing the preferred language.
			 * All other addresses are returned in the preferred language.
			 * Address components are all returned in the same language,
			 * which is chosen from the first component.
			 * If a name is not available in the preferred language, the geocoder uses the closest match.
			 * The preferred language has a small influence on the set of results
			 * that the API chooses to return, and the order in which they are returned.
			 * The geocoder interprets abbreviations differently depending on language,
			 * such as the abbreviations for street types,
			 * or synonyms that may be valid in one language but not in another.
			 * For example, utca and tér are synonyms for street in Hungarian.»
			 * https://developers.google.com/maps/documentation/geocoding/intro#geocoding
			 *
			 * Замечание №1
			 * Для Бразилии требуемое значение: «pt-BR».
			 * *) Параметр «language» не указан: https://maps.googleapis.com/maps/api/geocode/json?address=Av.+L%C3%BAcio+Costa%2C+3150+-+Barra+da+Tijuca%2C+Rio+de+Janeiro+-+RJ%2C+22630-010&key=AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc
			 * *) language=pt-BR: https://maps.googleapis.com/maps/api/geocode/json?language=pt-BR&address=Av.+L%C3%BAcio+Costa%2C+3150+-+Barra+da+Tijuca%2C+Rio+de+Janeiro+-+RJ%2C+22630-010&key=AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc
			 * Единственная разница: в написании названия страны: «Brazil» / «Brasil».
			 *
			 * Замечание №2
			 * Пробой выяснил, что API допускает указания значения параметра «language»
			 * в формате @uses df_locale() («pt_BR»)
			 * https://maps.googleapis.com/maps/api/geocode/json?language=pt_BR&address=Av.+L%C3%BAcio+Costa%2C+3150+-+Barra+da+Tijuca%2C+Rio+de+Janeiro+-+RJ%2C+22630-010&key=AIzaSyBj8bPt0PeSxcgPW8vTfNI2xKdhkHCUYuc
			 * И это не случайное совпадение: например, если указать значение «pt_BR1»,
			 * то API его уже не поймёт, и результат будет на английском языке.
			 */
			,$locale
		);
	}

	/**
	 * 2017-07-20
	 * @used-by self::p()
	 * @used-by \Dfe\Geo\Test\Basic::t01()
	 * @param A|string $a
	 * @param bool|F $onE [optional]
	 * @return AA|mixed
	 * @throws DFE
	 */
	function all($a, $onE = true) {
		if ($a instanceof A) {
			$a = df_csv_pretty($a->getStreet(), $a->getCity(), $a->getRegion(), $a->getPostcode());
		}
		return df_try(function() use($a):AA {return $this->_api->geocode($a);}, $this->onError($a, $onE));
	}

	/**
	 * 2017-07-20
	 * @used-by \Dfe\Moip\P\Charge::pAddress()
	 * @used-by \Dfe\Moip\Test\Data::ga()
	 * @param A|string $a
	 * @param bool|F $onE [optional]
	 * @return GA|mixed
	 * @throws DFE
	 */
	function p($a, $onE = true) {return
		!($all = $this->all($a, $onE)/** @var AA|mixed $all */) instanceof AA ? $all : df_try(
			function() use($all):GA {return $all->first();}, $this->onError($a, $onE)
		);
	}

	/**
	 * 2017-07-20
	 * It fixes the issue:
	 * «When a customer tries to place an order with a fake address,
	 * the system shows him a low level Google Maps Geocoding API error.
	 * The system should show an end-user message instead.»
	 * https://github.com/mage2pro/moip/issues/2
	 * https://sentry.io/dmitry-fedyuk/mage2pro/issues/314072005
	 * @used-by self::all()
	 * @used-by self::p()
	 * @param bool|F $f
	 * @return mixed
	 */
	private function onError(string $a, $f):F {return function(E $e) use($a, $f) {return $f instanceof F ? $f($e) :
		(true === $f ? df_error('Unable to recognize the address: «%1».', $a) : $f)
	;};}

	/**
	 * 2017-07-20
	 * @used-by self::__construct()
	 * @used-by self::p()
	 * @var API
	 */
	private $_api;
}