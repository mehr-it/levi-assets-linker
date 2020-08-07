<?php


	namespace MehrIt\LeviAssetsLinker\Contracts;


	use Psr\Http\Message\RequestInterface;

	interface AssetLinkFilter
	{
		/**
		 * Filters the paths array
		 * @param RequestInterface $request The request
		 * @param string[] $paths The paths
		 * @param array $options The filter options
		 * @return AssetLinkFilter This instance
		 */
		public function filterPaths(RequestInterface $request, array &$paths, array $options = []): AssetLinkFilter;

		/**
		 * Processes the link URL
		 * @param RequestInterface $request The request
		 * @param string $link The link URL
		 * @param array $options The filter options
		 * @return AssetLinkFilter This instance
		 */
		public function processLink(RequestInterface $request, string &$link, array $options = []): AssetLinkFilter;
	}