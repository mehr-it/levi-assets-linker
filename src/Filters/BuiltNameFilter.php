<?php


	namespace MehrIt\LeviAssetsLinker\Filters;


	use MehrIt\LeviAssetsLinker\Contracts\AssetLinkFilter;
	use Psr\Http\Message\RequestInterface;

	/**
	 * Filters paths by built name
	 * @package MehrIt\LeviAssetsLinker\Filters
	 */
	class BuiltNameFilter implements AssetLinkFilter
	{
		/**
		 * @inheritDoc
		 */
		public function filterPaths(RequestInterface $request, array &$paths, array $options = []): AssetLinkFilter {

			if (!array_filter($options))
				return $this;

			$paths = array_intersect_key($paths, array_fill_keys($options, true));

			return $this;
		}

		/**
		 * @inheritDoc
		 */
		public function processLink(RequestInterface $request, string &$link, array $options = []): AssetLinkFilter {

			return $this;
		}


	}