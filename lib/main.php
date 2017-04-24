<?php
use Geocoder\Provider\GoogleMaps as API;
use Ivory\HttpAdapter\CurlHttpAdapter as Adapter;
/**
 * 2017-04-24
 * @param string $key
 * @return API
 */
function df_geo($key) {return dfcf(function($key) {return new API(new Adapter,
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
	df_locale()
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
	 */
	,null
	// 2017-04-24
	// PHPDoc: «Whether to use an SSL connection (optional)».
	// Google Maps требует значение true.
	,true
	// 2017-04-24
	// Google Maps API Reference: «Your application's API key.
	// This key identifies your application for purposes of quota management.»
	// https://developers.google.com/maps/documentation/geocoding/intro#geocoding
	// «How to generate a key for the Google Maps Geocoding API?» https://mage2.pro/t/3828
	,$key
);}, [$key]);}