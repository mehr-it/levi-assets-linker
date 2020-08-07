<?php


	namespace MehrItLeviAssetsLinkerTest\Cases\Unit\Filters;


	use MehrIt\LeviAssetsLinker\Filters\PreferWebPFilter;
	use MehrItLeviAssetsLinkerTest\Cases\Unit\TestCase;
	use Nyholm\Psr7\Factory\Psr17Factory;

	class PreferWebPFilterTest extends TestCase
	{
		public function testFilterPaths_webpAccepted() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de')->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');

			$filter = new PreferWebPFilter();

			$paths = [
				'build1' => 'the/first/path.txt',
				'build2' => 'the/second/path.txt',
				'build3' => 'the/third/path.txt',
				'build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, ['build1', 'build4']);

			$this->assertSame(
				[
					'build1' => 'the/first/path.txt',
					'build4' => 'the/fourth/path.txt',
					'build2' => 'the/second/path.txt',
					'build3' => 'the/third/path.txt',
				],
				$paths
			);

		}

		public function testFilterPaths_webpAccepted_noWebpBuilds() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de')->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');

			$filter = new PreferWebPFilter();

			$paths = [
				'build1' => 'the/first/path.txt',
				'build2' => 'the/second/path.txt',
				'build3' => 'the/third/path.txt',
				'build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, ['otherBuild']);

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

		public function testFilterPaths_webpAccepted_noWebpBuildsGiven() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de')->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');

			$filter = new PreferWebPFilter();

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

		public function testFilterPaths_webpNotAccepted() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de')->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');

			$filter = new PreferWebPFilter();

			$paths = [
				'build1' => 'the/first/path.txt',
				'build2' => 'the/second/path.txt',
				'build3' => 'the/third/path.txt',
				'build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, ['build1', 'build4']);

			$this->assertSame(
				[
					'build2' => 'the/second/path.txt',
					'build3' => 'the/third/path.txt',
				],
				$paths
			);

		}

		public function testFilterPaths_webpNotAccepted_noWebpBuildsGiven() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de')->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');

			$filter = new PreferWebPFilter();

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

		public function testFilterPaths_webpNotAccepted_noWebpBuilds() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de')->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');

			$filter = new PreferWebPFilter();

			$paths = [
				'build1' => 'the/first/path.txt',
				'build2' => 'the/second/path.txt',
				'build3' => 'the/third/path.txt',
				'build4' => 'the/fourth/path.txt',
			];

			$filter->filterPaths($rq, $paths, ['otherBuild']);

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

			$filter = new PreferWebPFilter();

			$link = 'https://test.de/the/first/path.txt';


			$filter->processLink($rq, $link, ['build1', 'build2']);


			$this->assertSame('https://test.de/the/first/path.txt', $link);

		}

		public function testProcessLink_noWebpBuildsGiven() {

			$rq = (new Psr17Factory())->createRequest('GET', 'https://test.de');

			$filter = new PreferWebPFilter();

			$link = 'https://test.de/the/first/path.txt';


			$filter->processLink($rq, $link, []);


			$this->assertSame('https://test.de/the/first/path.txt', $link);

		}
	}