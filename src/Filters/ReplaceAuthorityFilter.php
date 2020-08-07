<?php


	namespace MehrIt\LeviAssetsLinker\Filters;


	use MehrIt\LeviAssetsLinker\Contracts\AssetLinkFilter;
	use Psr\Http\Message\RequestInterface;

	class ReplaceAuthorityFilter implements AssetLinkFilter
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

			$hostName = $options[0] ?? null;

			if ($hostName)
				$link = preg_replace('/^(.*?:\\/\\/)([^\\/]*)/', "\$1{$hostName}", $link);

			return $this;
		}


	}