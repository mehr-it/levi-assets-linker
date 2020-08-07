<?php


	namespace MehrIt\LeviAssetsLinker\Filters;


	use MehrIt\LeviAssetsLinker\Contracts\AssetLinkFilter;
	use Psr\Http\Message\RequestInterface;

	/**
	 * Prefers the build names given as arguments if browser accepts 'image/webp'. If the browser does not accept 'image/webp', the corresponding builds are removed when others exist
	 * @package MehrIt\LeviAssetsLinker\Filters
	 */
	class PreferWebPFilter implements AssetLinkFilter
	{
		/**
		 * @inheritDoc
		 */
		public function filterPaths(RequestInterface $request, array &$paths, array $options = []): AssetLinkFilter {

			if (!array_filter($options))
				return $this;



			if (strpos($request->getHeaderLine('Accept'), 'image/webp') !== false) {
				// webp is accepted => return webp builds first, so that they are preferred

				$paths = array_merge(...$this->groupWebpBuilds($paths, $options));

			}
			else {
				// web is not accepted => remove webp builds if we would not wipe everything

				$paths = $this->groupWebpBuilds($paths, $options)[1] ?: $paths;
			}

			return $this;
		}

		/**
		 * @inheritDoc
		 */
		public function processLink(RequestInterface $request, string &$link, array $options = []): AssetLinkFilter {

			return $this;
		}


		/**
		 * Splits the given path array in two arrays, one containing the webp builds, the other containing non-webp builds
		 * @param array $paths The paths
		 * @param array $webpBuilds The names of the webp builds
		 * @return array|array[] Two arrays. First contains the webp builds, the second contains non-webp builds
		 */
		protected function groupWebpBuilds(array $paths, array $webpBuilds): array {

			$webpBuildMap = array_fill_keys($webpBuilds, true);

			$webP = [];
			$noWebP = [];

			foreach($paths as $buildName => $path) {

				if ($webpBuildMap[$buildName] ?? false)
					$webP[$buildName] = $path;
				else
					$noWebP[$buildName] = $path;

			}

			return [$webP, $noWebP];
		}

	}