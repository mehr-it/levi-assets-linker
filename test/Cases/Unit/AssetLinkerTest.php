<?php


	namespace MehrItLeviAssetsLinkerTest\Cases\Unit;


	use MehrIt\LeviAssetsLinker\AssetLinker;
	use MehrIt\LeviAssetsLinker\Filters\ReplaceSchemeFilter;
	use Nyholm\Psr7\Factory\Psr17Factory;

	class AssetLinkerTest extends TestCase
	{

		public function testLink_withoutFilters() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [];

			$link = AssetLinker::link($rq, $paths, $filters);

			$this->assertSame('https://test.de/the/first/path.txt', $link);
		}

		public function testLink_withRoot() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'root' => 'root/path'
			]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [];

			$link = AssetLinker::link($rq, $paths, $filters);

			$this->assertSame('https://test.de/root/path/the/first/path.txt', $link);
		}

		public function testLink_withQuery_string() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'root' => 'root/path'
			]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [];

			$link = AssetLinker::link($rq, $paths, $filters, 'test=5');

			$this->assertSame('https://test.de/root/path/the/first/path.txt?test=5', $link);
		}

		public function testLink_withQuery_stringPrefixedWithQuestionMark() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'root' => 'root/path'
			]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [];

			$link = AssetLinker::link($rq, $paths, $filters, '?test=5');

			$this->assertSame('https://test.de/root/path/the/first/path.txt?test=5', $link);
		}

		public function testLink_withQuery_array() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'root' => 'root/path'
			]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [];

			$link = AssetLinker::link($rq, $paths, $filters, ['test' => 5, 'x' => 0]);

			$this->assertSame('https://test.de/root/path/the/first/path.txt?test=5&x=0', $link);
		}

		public function testLink_withQuery_emptyArray() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'root' => 'root/path'
			]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [];

			$link = AssetLinker::link($rq, $paths, $filters, []);

			$this->assertSame('https://test.de/root/path/the/first/path.txt', $link);
		}

		public function testLink_withDefaultFilters() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'default_filters' => [
					['built', 'b', 'c'],
					['host', 'other.de'],
					['built', 'a', 'b'],
					['proto', 'http'],
				]
			]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [];

			$link = AssetLinker::link($rq, $paths, $filters);

			$this->assertSame('http://other.de/the/second/path.txt', $link);
		}

		public function testLink_withDefaultFiltersAndFilters() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'default_filters' => [
					['built', 'b', 'c'],
					['host', 'other.de'],
				]
			]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [
				['built', 'a', 'b'],
				['proto', 'http'],
			];

			$link = AssetLinker::link($rq, $paths, $filters);

			$this->assertSame('http://other.de/the/second/path.txt', $link);
		}

		public function testLink_withFilters() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [
				['built', 'b', 'c'],
				['host', 'other.de'],
				['built', 'a', 'b'],
				['proto', 'http'],
			];

			$link = AssetLinker::link($rq, $paths, $filters);

			$this->assertSame('http://other.de/the/second/path.txt', $link);
		}

		public function testLink_withFiltersAndRoot() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'root' => 'my/root'
			]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [
				['built', 'b', 'c'],
				['host', 'other.de'],
				['built', 'a', 'b'],
				['proto', 'http'],
			];

			$link = AssetLinker::link($rq, $paths, $filters);

			$this->assertSame('http://other.de/my/root/the/second/path.txt', $link);
		}

		public function testLink_withFiltersAndRootMultipleRequests() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'root' => 'my/root'
			]);

			$linker = AssetLinker::instance();

			$paths1 = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];
			$paths2 = [
				'a' => 'another/first/path.txt',
				'b' => 'another/second/path.txt',
				'c' => 'another/third/path.txt',
			];

			$filters1 = [
				['built', 'b', 'c'],
				['host', 'other.de'],
				['built', 'a', 'b'],
				['proto', 'http'],
			];
			$filters2 = [
				['built', 'b', 'c'],
			];

			$link1 = $linker->linkAsset($rq, $paths1, $filters1);
			$this->assertSame('http://other.de/my/root/the/second/path.txt', $link1);

			$link2 = $linker->linkAsset($rq, $paths2, $filters2);
			$this->assertSame('https://test.de/my/root/another/second/path.txt', $link2);
		}

		public function testLink_withCustomFilters() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'filters' => [
					'custom1' => ReplaceSchemeFilter::class,
				]
			]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [
				['built', 'b', 'c'],
				['host', 'other.de'],
				['built', 'a', 'b'],
				['proto', 'http'],
				['custom1', 'ftp'],
			];

			$link = AssetLinker::link($rq, $paths, $filters);

			$this->assertSame('ftp://other.de/the/second/path.txt', $link);
		}

		public function testLink_withOverwrittenDefaultFilter() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'filters' => [
					'host' => ReplaceSchemeFilter::class,
				]
			]);

			$paths = [
				'a' => 'the/first/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [
				['built', 'b', 'c'],
				['built', 'a', 'b'],
				['host', 'http'],
			];

			$link = AssetLinker::link($rq, $paths, $filters);

			$this->assertSame('http://test.de/the/second/path.txt', $link);
		}

		public function testLink_pathRequiresEncode() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			AssetLinker::configure([
				'root' => 'my path'
			]);

			$paths = [
				'a' => 'the/first?/path.txt',
				'b' => 'the/second/path.txt',
				'c' => 'the/third/path.txt',
			];

			$filters = [];

			$link = AssetLinker::link($rq, $paths, $filters);

			$this->assertSame('https://test.de/my%20path/the/first%3F/path.txt', $link);
		}

		public function testInstanceSingleton() {

			AssetLinker::configure([]);

			$instance = AssetLinker::instance();

			$this->assertSame($instance, AssetLinker::instance());

			// reconfigure => instance should be reset
			AssetLinker::configure([]);
			$this->assertNotSame($instance, AssetLinker::instance());
		}

		public function testInstanceResolvesConfiguredClass() {

			AssetLinker::configure([
				'class' => AssetLinkerTestLinkerMock::class,
			]);

			$this->assertInstanceOf(AssetLinkerTestLinkerMock::class, AssetLinker::instance());

		}

	}

	class AssetLinkerTestLinkerMock extends AssetLinker {


	}