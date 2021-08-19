# Levi assets linker
[![Latest Version on Packagist](https://img.shields.io/packagist/v/mehr-it/levi-assets-linker.svg?style=flat-square)](https://packagist.org/packages/mehr-it/levi-assets-linker)
[![Build Status](https://travis-ci.org/mehr-it/levi-assets-linker.svg?branch=master)](https://travis-ci.org/mehr-it/levi-assets-linker)

Framework independent linker component for the [mehr-it/levi-assets](https://packagist.org/packages/mehr-it/levi-assets) 
package. It allows to generate links from passed asset build paths outside of the Laravel framework. 

The concept behind this separate linker component allows to generate all necessary page data using
the laravel application and store it for a separate request processor app. This app can be very
lightweight without any framework overhead. It can use the linker component only, to resolve
asset links on the fly.

## Installation

You can install the package via composer:

    composer require mehr-it/levi-assets-linker

## The role of the linker

Despite generating links, the job of the linker is to pick the most suitable asset built for the given
asset. Therefore it receives a list with the paths to all builds of an asset to pick one for the link.
Without any filter, the linker would simply pick the first built to generate a link for. However, 
link filters may modify (sort, remove, ...) the built list. They can also modify the generated URL
after the linker has generated it from the path.


## Linker configuration

The linker can be configured using the static `configure()` method:

    AssetLinker::configure([
    
        // configures the path prefix prepended to all asset paths
        'root' => 'my/assets',
        
        // allows to define a custom implementaion of the AssetLinker
        'class' => MyAssetLinker::class,
        
        // defines the default link filters to apply when generating links
        'default_filters' => [
        
            // the first item specifies the filter name, all others are passed as arguments
            ['proto', 'https'],
            ['host', 'cdn.my-page.com'],
        ],
        
        // allows to register custom filter classes
        'filters' => [
            'myFilter' => MyCustomFilter::class,
        ] 
    ]);
    
Each call to `configure()` will reset any previous configuration.


## Linking assets

The static `link` method generates links using the previously set configuration:

    $link = AssetLinker::link($request, $paths, $linkFilters, $query);
    
The first argument is expected to hold a PSR-7 compatible representation of the request. This request
object is passed to all link filters to they can pick the most suitable asset build.

The second argument must contain an associative array with all asset builds' paths. The build name is
used as array key.

The `$linkFilters` argument allows to apply custom filters for this call. It must be an array
of filter definitions. Each filter definition is an array itself and consists of the filter name
(as defined in the *filters* option of the config) followed by all the arguments to be passed
to the filter. Filters are applied in the oder they are passed. The configured `'default_filters'`
are applied before the ones passed as argument.

The last argument allows to add query parameters to the URL. They can be passed as array or as already
encoded query string.


## Built-in filters
The linker package comes with some predefined filters which can be used out of the box.

### built
The `BuiltNameFilter` allows to specify a which builds of the assets can be used for the link. 
Following example filters for jpg builds only:

    $paths = [
        'jpg_small' => 'small/image.jpg',
        'png_small' => 'small/image.png',
        'jpg_large' => 'large/image.jpg',
        'png_large' => 'large/image.png',
    ];
    
    AssetLinker::link($request, $paths, [['built', 'jpg_small', 'jpg_large']]);
    
### pfx
The `BuiltNamePrefixFilter` allows to specify a which builds of the assets can be used by applying a
prefix search. Following example filters for jpg builds only:

    $paths = [
        'jpg_small' => 'small/image.jpg',
        'png_small' => 'small/image.png',
        'jpg_large' => 'large/image.jpg',
        'png_large' => 'large/image.png',
    ];
    
    AssetLinker::link($request, $paths, [['pfx', 'jpg']]);
    
### host
The `ReplaceAuthorityFilter` allows to overwrite the host (authority) part the generated URL. This is
needed, when another host name should be used for assets. Default is to use the same as the 
request host name.
    
    AssetLinker::link($request, $paths, [['host', 'www.example.com']]);
    
### proto
The `ReplaceSchemeFilter` allows to overwrite the protocol (scheme) part the generated URL. This 
can be used to force HTTPS. Default is to use the same as the request scheme.
    
    AssetLinker::link($request, $paths, [['proto', 'https']]);

### replacePath

The `ReplacePathFilter` allows to do a regex replacement for the path component of the generated URL. This 
might be useful if e.g. you need to a prefix to the path. **Note: the path always starts with "/" if not empty**

    // prepend prefix "_pfx" to the path
    AssetLinker::link($request, $paths, [['replacePath', '%^(/.*$%', '_pfx$1']]);

### webp
The `PreferWebPFilter` allows to prefer webp images over other formats when the browser supports 
them. The arguments must identify the webp image builds by their name.  
    
    $paths = [
            'webp_small' => 'small/image.webp',
            'png_small'  => 'small/image.png',
            'webp_large' => 'large/image.webp',
            'png_large'  => 'large/image.png',
        ];
    
    AssetLinker::link($request, $paths, [['webp', 'webp_small', 'webp_large']]);
    
If the browser has included 'image/webp' to the request's accept header, the builts "webp_small" and
"webp_large" will be sorted first in the builds array.

If the browser does not indicate webp support, webp builds are removed from the list, **if there are
others available**.