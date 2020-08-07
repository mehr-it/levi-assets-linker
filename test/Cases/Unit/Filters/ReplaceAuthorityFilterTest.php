<?php


	namespace MehrItLeviAssetsLinkerTest\Cases\Unit\Filters;


	use MehrIt\LeviAssetsLinker\Filters\ReplaceAuthorityFilter;
	use MehrItLeviAssetsLinkerTest\Cases\Unit\TestCase;
	use Nyholm\Psr7\Factory\Psr17Factory;

	class ReplaceAuthorityFilterTest extends TestCase
	{

		public function testFilterPaths() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplaceAuthorityFilter();

			$paths = [
				'build1' => 'the/first/path.txt',
				'build2' => 'the/second/path.txt',
				'build3' => 'the/third/path.txt',
				'build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, ['www.other.de']);

			$this->assertSame(
				[
					'build1' => 'the/first/path.txt',
					'build2' => 'the/second/path.txt',
					'build3' => 'the/third/path.txt',
					'build4' => 'the/fourth/path.txt',
				],
				$paths
			);

		}

		public function testFilterPaths_noAuthorityGiven() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplaceAuthorityFilter();

			$paths = [
				'build1' => 'the/first/path.txt',
				'build2' => 'the/second/path.txt',
				'build3' => 'the/third/path.txt',
				'build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, []);

			$this->assertSame(
				[
					'build1' => 'the/first/path.txt',
					'build2' => 'the/second/path.txt',
					'build3' => 'the/third/path.txt',
					'build4' => 'the/fourth/path.txt',
				],
				$paths
			);

		}

		public function testProcessLink() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplaceAuthorityFilter();

			$link = 'https://test.de/the/first/path.txt';


			$filter->processLink($rq, $link, ['www.other.de']);


			$this->assertSame('https://www.other.de/the/first/path.txt', $link);

		}

		public function testProcessLink_replaceFullAuthority() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplaceAuthorityFilter();

			$link = 'https://user:pass@www.test.de:80/the/first/path.txt';


			$filter->processLink($rq, $link, ['www.other.de']);


			$this->assertSame('https://www.other.de/the/first/path.txt', $link);

		}

		public function testProcessLink_noAuthorityGiven() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplaceAuthorityFilter();

			$link = 'https://test.de/the/first/path.txt';


			$filter->processLink($rq, $link, []);


			$this->assertSame('https://test.de/the/first/path.txt', $link);

		}

	}