<?php

	namespace MehrItLeviAssetsLinkerTest\Cases\Unit\Filters;

	use MehrIt\LeviAssetsLinker\Filters\ReplacePathFilter;
	use MehrItLeviAssetsLinkerTest\Cases\Unit\TestCase;
	use Nyholm\Psr7\Factory\Psr17Factory;
	use Throwable;

	class ReplacePathFilterTest extends TestCase
	{

		public function testFilterPaths() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplacePathFilter();

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

		public function testProcessLink() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplacePathFilter();

			$link = 'https://test.de/the/first/path.txt';


			$filter->processLink($rq, $link, ['%^(/.*)$%', '/_pfx$1']);


			$this->assertSame('https://test.de/_pfx/the/first/path.txt', $link);

		}
		
		public function testProcessLink_resultMissesPrecedingSlash() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplacePathFilter();

			$link = 'https://test.de/the/first/path.txt';


			$filter->processLink($rq, $link, ['%^(/.*)$%', '_pfx$1']);


			$this->assertSame('https://test.de/_pfx/the/first/path.txt', $link);

		}
		
		public function testProcessLink_invalidPattern() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplacePathFilter();

			$link = 'https://test.de/the/first/path.txt';


			try {
				$filter->processLink($rq, $link, ['%^(/.*$%', '_pfx$1']);
			}
			catch(Throwable $e) {
				
			}


			$this->assertSame('https://test.de/the/first/path.txt', $link);

		}
		
		public function testProcessLink_missingReplace() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplacePathFilter();

			$link = 'https://test.de/the/first/path.txt';


			try {
				$filter->processLink($rq, $link, ['%^(/.*$%']);
			}
			catch(Throwable $e) {
				
			}


			$this->assertSame('https://test.de/the/first/path.txt', $link);

		}
		
		public function testProcessLink_missingPattern() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new ReplacePathFilter();

			$link = 'https://test.de/the/first/path.txt';


			try {
				$filter->processLink($rq, $link);
			}
			catch(Throwable $e) {
				
			}


			$this->assertSame('https://test.de/the/first/path.txt', $link);

		}
		
	}