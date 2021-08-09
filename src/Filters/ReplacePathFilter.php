<?php

	namespace MehrIt\LeviAssetsLinker\Filters;

	use MehrIt\LeviAssetsLinker\Contracts\AssetLinkFilter;
	use MehrIt\LeviAssetsLinker\UrlHelper;
	use Psr\Http\Message\RequestInterface;

	class ReplacePathFilter implements AssetLinkFilter
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

			$pattern = $options[0] ?? null;
			$replace = $options[1] ?? null;

			if ($pattern !== null && $replace !== null) {
				$linkElements = UrlHelper::parseUrl($link);

				$path = preg_replace($pattern, $replace, $linkElements['path'] ?? '');
				if ($path === null) {
					return $this;
				}
				if ($path === '') {
					$path = null;
				}
				elseif ($path[0] !== '/') {
					$path = "/{$path}";
				}

				$linkElements['path'] = $path;

				$link = UrlHelper::buildUrl($linkElements);
			}


			return $this;
		}


	}