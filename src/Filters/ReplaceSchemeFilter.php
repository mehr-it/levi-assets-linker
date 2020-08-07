<?php


	namespace MehrIt\LeviAssetsLinker\Filters;


	use MehrIt\LeviAssetsLinker\Contracts\AssetLinkFilter;
	use Psr\Http\Message\RequestInterface;

	class ReplaceSchemeFilter implements AssetLinkFilter
	{
		/**
		 * @inheritDoc
		 */
		public function filterPaths(RequestInterface $request, array &$paths, array $options = []): AssetLinkFilter {

			return $this;
		}

		/**
		 * @inheritDoc
		 */
		public function processLink(RequestInterface $request, string &$link, array $options = []): AssetLinkFilter {

			$protocol = $options[0] ?? null;

			if ($protocol)
				$link = preg_replace('/^(.*?:\\/\\/)/', "{$protocol}://", $link);

			return $this;
		}


	}