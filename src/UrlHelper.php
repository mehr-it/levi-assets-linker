<?php

	namespace MehrIt\LeviAssetsLinker;

	class UrlHelper
	{

		/**
		 * Parses the given URL (@see parse_url())
		 * @param string $url The URL
		 * @return array The URL elements
		 */
		public static function parseUrl(string $url): array {
			return parse_url($url);
		}

		/**
		 * Builds a URL (reverse of parseUrl())
		 * @param array $elements The URL elements returned as associative array. Array keys are 'scheme', 'host', 'port', 'user', 'pass', 'path', 'query' and 'fragment'
		 * @return string The URL
		 * 
		 */
		public static function buildUrl(array $elements): string {
			$scheme   = isset($elements['scheme']) ? "{$elements['scheme']}://" : '';
			$host     = $elements['host'] ?? '';
			$port     = isset($elements['port']) ? ":{$elements['port']}" : '';
			$user     = $elements['user'] ?? '';
			$pass     = isset($elements['pass']) ? ":{$elements['pass']}" : '';
			$pass     = ($user || $pass) ? "{$pass}@" : '';
			$path     = $elements['path'] ?? '';
			$query    = isset($elements['query']) ? "?{$elements['query']}" : '';
			$fragment = isset($elements['fragment']) ? "#{$elements['fragment']}" : '';

			return "$scheme$user$pass$host$port$path$query$fragment";
		}
	}