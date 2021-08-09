<?php


	namespace MehrIt\LeviAssetsLinker;


	use MehrIt\LeviAssetsLinker\Contracts\AssetLinkFilter;
	use MehrIt\LeviAssetsLinker\Contracts\AssetLinker as AssetLinkerContract;
	use MehrIt\LeviAssetsLinker\Filters\BuiltNameFilter;
	use MehrIt\LeviAssetsLinker\Filters\BuiltNamePrefixFilter0;
	use MehrIt\LeviAssetsLinker\Filters\PreferWebPFilter;
	use MehrIt\LeviAssetsLinker\Filters\ReplaceAuthorityFilter;
	use MehrIt\LeviAssetsLinker\Filters\ReplacePathFilter;
	use MehrIt\LeviAssetsLinker\Filters\ReplaceSchemeFilter;
	use Psr\Http\Message\RequestInterface;
	use RuntimeException;

	class AssetLinker implements AssetLinkerContract
	{
		const DEFAULT_FILTERS = [
			'built'       => BuiltNameFilter::class,
			'host'        => ReplaceAuthorityFilter::class,
			'pfx'         => BuiltNamePrefixFilter0::class,
			'proto'       => ReplaceSchemeFilter::class,
			'replacePath' => ReplacePathFilter::class,
			'webp'        => PreferWebPFilter::class,
		];

		protected static $config = null;

		/**
		 * @var AssetLinkerContract|null
		 */
		protected static $instance;


		/**
		 * Resets all static properties to its initial state
		 */
		public static function reset() {
			static::$config   = null;
			static::$instance = null;
		}

		/**
		 * Sets the global configuration for the asset linker
		 * @param array $config The global configuration
		 */
		public static function configure(array $config) {

			static::reset();

			$config['filters'] = array_merge(static::DEFAULT_FILTERS, $config['filters'] ?? []);

			static::$config = $config;
		}

		/**
		 * Gets the config
		 * @return array The config
		 */
		protected static function config(): array {

			// init with default config if not configured yet
			if (static::$config === null)
				static::configure([]);

			return static::$config;
		}

		/**
		 * Gets the linker instance
		 * @return AssetLinkerContract The linker instance
		 */
		public static function instance(): AssetLinkerContract {

			if (!static::$instance) {

				$config = static::config();

				$cls = $config['class'] ?? AssetLinker::class;

				static::$instance = new $cls;

				static::$instance->setDefaultLinkFilters($config['default_filters'] ?? []);
			}

			return static::$instance;
		}

		/**
		 * Gets the link URL by choosing the most appropriate build and generating a link
		 * @param RequestInterface $request The request to resolve the asset for
		 * @param string[] $paths The build assets public paths
		 * @param mixed[][]|string[][] $linkFilters Link filter definitions. Each item contains a filter definition as array. The first item of the definition is the filter name as configured.
		 * @param string|array|null $query The query to parameters to add to the link
		 * @return string|null The link URL. Null if not resolvable
		 */
		public static function link(RequestInterface $request, array $paths, array $linkFilters = [], $query = null): ?string {
			return static::instance()->linkAsset($request, $paths, $linkFilters, $query);
		}


		protected $filters = [];

		protected $pathPrefix;

		protected $defaultLinkFilters;


		/**
		 * @inheritDoc
		 */
		public function setDefaultLinkFilters(array $defaultLinkFilters): AssetLinkerContract {
			$this->defaultLinkFilters = $defaultLinkFilters;

			return $this;
		}


		/**
		 * @inheritDoc
		 */
		public function linkAsset(RequestInterface $request, array $paths, array $linkFilters, $query = null): ?string {

			// prepare filters
			$filters = [];
			foreach (array_merge($this->defaultLinkFilters, $linkFilters) as $currFilterDef) {

				$filters[] = [
					$this->filter(array_shift($currFilterDef)),
					$currFilterDef
				];
			}

			// filter paths
			foreach ($filters as [$filter, $options]) {
				/** @var AssetLinkFilter $filter */
				$filter->filterPaths($request, $paths, $options);
			}

			if (!count($paths))
				return null;

			// build link
			$requestUri = $request->getUri();
			$link       = "{$requestUri->getScheme()}://{$request->getUri()->getAuthority()}/{$this->pathPrefix()}{$this->urlEncodePath(reset($paths))}{$this->buildQuery($query)}";

			// process link
			foreach ($filters as [$filter, $options]) {
				/** @var AssetLinkFilter $filter */
				$filter->processLink($request, $link, $options);
			}

			return $link;
		}

		/**
		 * Gets the root prefix for the path
		 * @return string The root prefix for the path
		 */
		protected function pathPrefix(): string {
			if ($this->pathPrefix === null) {
				$root = static::config()['root'] ?? null;
				if ($root !== null)
					$this->pathPrefix = "{$this->urlEncodePath($root)}/";
				else
					$this->pathPrefix = '';
			}

			return $this->pathPrefix;
		}

		/**
		 * Resolves the filter instance by name
		 * @param string $name The filter name
		 * @return AssetLinkFilter
		 */
		protected function filter(string $name): AssetLinkFilter {

			$filter = $this->filters[$name] ?? null;

			if (!$filter) {

				$cls = static::config()['filters'][$name] ?? $name;

				$filter = new $cls();

				if (!($filter instanceof AssetLinkFilter))
					throw new RuntimeException("Link filter \"{$name}\" was resolved to \"{$cls}\" but it does not implement " . AssetLinkFilter::class);

				$this->filters[$name] = $filter;
			}

			return $filter;
		}

		/**
		 * Applies rawurlencode() to all path segments
		 * @param string $path The path
		 * @return string The URL encoded path
		 */
		protected function urlEncodePath(string $path): string {
			return implode('/', array_map('rawurlencode', explode('/', $path)));
		}

		/**
		 * Builds the query string to append to a URL
		 * @param string|array|null $query The query
		 * @return string The query part of a URL
		 */
		protected function buildQuery($query): string {

			if ($query === null) {
				return '';
			}
			elseif (is_array($query)) {

				if (!count($query))
					return '';

				return '?' . http_build_query($query);
			}
			else {
				$query = (string)$query;

				if (($query[0] ?? null) !== '?')
					$query = "?{$query}";

				return $query;
			}
		}
	}