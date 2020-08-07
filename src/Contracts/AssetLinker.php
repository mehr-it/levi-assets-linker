<?php


	namespace MehrIt\LeviAssetsLinker\Contracts;


	use Psr\Http\Message\RequestInterface;

	interface AssetLinker
	{
		/**
		 * Sets the default link filters
		 * @param array $defaultLinkFilters
		 * @return AssetLinker
		 */
		public function setDefaultLinkFilters(array $defaultLinkFilters): AssetLinker;


		/**
		 * Gets the link URL by choosing the most appropriate build and generating a link
		 * @param RequestInterface $request The request to resolve the asset for
		 * @param string[] $paths The build assets public paths
		 * @param mixed[][]|string[][] $linkFilters Link filter definitions. Each item contains a filter definition as array. The first item of the definition is the filter name as configured.
		 * @param string|array|null $query The query to parameters to add to the link
		 * @return string|null The link URL. Null if not resolvable
		 */
		public function linkAsset(RequestInterface $request, array $paths, array $linkFilters, $query = null): ?string;


	}