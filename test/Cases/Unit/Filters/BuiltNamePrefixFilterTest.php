<?php


	namespace MehrItLeviAssetsLinkerTest\Cases\Unit\Filters;


	use MehrIt\LeviAssetsLinker\Filters\BuiltNamePrefixFilter0;
	use MehrItLeviAssetsLinkerTest\Cases\Unit\TestCase;
	use Nyholm\Psr7\Factory\Psr17Factory;

	class BuiltNamePrefixFilterTest extends TestCase
	{
		public function testFilterPaths() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNamePrefixFilter0();

			$paths = [
				'a_build1'  => 'the/first/path.txt',
				'a_build2'  => 'the/second/path.txt',
				'b_build3'  => 'the/third/path.txt',
				'c1_build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, ['a_', 'c1_']);

			$this->assertSame(
				[
					'a_build1'  => 'the/first/path.txt',
					'a_build2'  => 'the/second/path.txt',
					'c1_build4' => 'the/fourth/path.txt',
				],
				$paths
			);

		}

		public function testFilterPaths_noneMatching() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNamePrefixFilter0();

			$paths = [
				'a_build1' => 'the/first/path.txt',
				'a_build2' => 'the/second/path.txt',
				'b_build3' => 'the/third/path.txt',
				'c_build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, ['build', 'd_']);

			$this->assertSame([], $paths);

		}

		public function testFilterPaths_pathsEmpty() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNamePrefixFilter0();

			$paths = [];

			$filter->filterPaths($rq, $paths, ['a_']);

			$this->assertSame([], $paths);

		}

		public function testFilterPaths_noFiltersGiven() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNamePrefixFilter0();

			$paths = [
				'a_build1' => 'the/first/path.txt',
				'a_build2' => 'the/second/path.txt',
				'b_build3' => 'the/third/path.txt',
				'c_build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, []);

			$this->assertSame(
				[
					'a_build1' => 'the/first/path.txt',
					'a_build2' => 'the/second/path.txt',
					'b_build3' => 'the/third/path.txt',
					'c_build4' => 'the/fourth/path.txt',
				],
				$paths
			);

		}

		public function testProcessLink() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNamePrefixFilter0();

			$link = 'https://test.de/the/first/path.txt';


			$filter->processLink($rq, $link, ['build1', 'build2']);


			$this->assertSame('https://test.de/the/first/path.txt', $link);

		}

		public function testProcessLink_noFiltersGiven() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new BuiltNamePrefixFilter0();

			$link = 'https://test.de/the/first/path.txt';


			$filter->processLink($rq, $link, []);


			$this->assertSame('https://test.de/the/first/path.txt', $link);

		}
	}