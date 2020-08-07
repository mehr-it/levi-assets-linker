<?php


	namespace MehrIt\LeviAssetsLinker\Filters;


	use MehrIt\LeviAssetsLinker\Contracts\AssetLinkFilter;
	use Psr\Http\Message\RequestInterface;

	/**
	 * Filters paths by prefix of the built name
	 * @package MehrIt\LeviAssetsLinker\Filters
	 */
	class BuiltNamePrefixFilter0 implements AssetLinkFilter
	{
		/**
		 * @inheritDoc
		 */
		public function filterPaths(RequestInterface $request, array &$paths, array $options = []): AssetLinkFilter {

			if (!array_filter($options))
				return $this;

			$filteredPaths = [];
			foreach($paths as $buildName => $path) {
				foreach($options as $currPrefix) {
					if (substr($buildName, 0, strlen($currPrefix)) === $currPrefix) {
						$filteredPaths[$buildName] = $path;
						continue 2;
					}
				}
			}

			$paths = $filteredPaths;

			return $this;
		}

		/**
		 * @inheritDoc
		 */
		public function processLink(RequestInterface $request, string &$link, array $options = []): AssetLinkFilter {

			return $this;
		}


	}