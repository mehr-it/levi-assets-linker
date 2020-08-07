<?php


	namespace MehrItLeviAssetsLinkerTest\Cases\Unit\Filters;


	use MehrIt\LeviAssetsLinker\Filters\BuiltNameFilter;
	use MehrItLeviAssetsLinkerTest\Cases\Unit\TestCase;
	use Nyholm\Psr7\Factory\Psr17Factory;

	class BuiltNameFilterTest extends TestCase
	{

		public function testFilterPaths() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNameFilter();

			$paths = [
				'build1' => 'the/first/path.txt',
				'build2' => 'the/second/path.txt',
				'build3' => 'the/third/path.txt',
				'build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, ['build1', 'build3']);

			$this->assertSame(
				[
					'build1' => 'the/first/path.txt',
					'build3' => 'the/third/path.txt',
				],
				$paths
			);

		}

		public function testFilterPaths_noneMatching() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNameFilter();

			$paths = [
				'build1' => 'the/first/path.txt',
				'build2' => 'the/second/path.txt',
				'build3' => 'the/third/path.txt',
				'build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, ['otherBuild']);

			$this->assertSame([], $paths);

		}

		public function testFilterPaths_pathsEmpty() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNameFilter();

			$paths = [];

			$filter->filterPaths($rq, $paths, ['build1']);

			$this->assertSame([], $paths);

		}

		public function testFilterPaths_noFiltersGiven() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNameFilter();

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

			$filter = new BuiltNameFilter();

			$link = 'https://test.de/the/first/path.txt';


			$filter->processLink($rq, $link, ['build1', 'build2']);


			$this->assertSame('https://test.de/the/first/path.txt', $link);

		}

		public function testProcessLink_noFiltersGiven() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNameFilter();

			$link = 'https://test.de/the/first/path.txt';


			$filter->processLink($rq, $link, []);


			$this->assertSame('https://test.de/the/first/path.txt', $link);

		}

	}