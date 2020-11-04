[![Latest Version](https://img.shields.io/github/v/release/jedamzik/statamic-toc?style=flat-square)](https://github.com/jedamzik/statamic-toc/releases)
![Tests](https://img.shields.io/github/workflow/status/jedamzik/statamic-toc/run-tests?label=Tests&style=flat-square)

# Toc

> Auto-Generated Table Of Contents from Markdown Fields for Statamic 3.

This generates a Table Of Contents from a Markdown field.

A list of Headings is saved in the entry as html and can either be rendered directly using `{{ table_of_contents }}` or through the provided tag `{{ toc }}` with a customizable wrapper view.

## Installation

Require it using Composer.

```
composer require jedamzik/statamic-toc
```

### Set up a Collection

Publish the wrapper view and package configuration from `Njed\Toc\ServiceProvider`:

```bash
php artisan vendor:publish
```

Provide a collection handle and a field handle in `config/toc.php` to activate `Toc` for a given collection:

```php
return [
    'collections' => [
        'posts' => 'content'
    ],
    ...
];
```

## Heading Depth

By default only `h1` and `h2` are used for the Table of Contents. If you want to include further heading levels, add them to the config:

```php
return [
    ...
    'includeLevels' => [3, 4]
];
```

List items for all headings with a level `> 2` will have a `.child` class added so you can style them separately.

## Anchor Links for Headings

Items in the Table of Contents can function as anchor links to the heading fragment:

```php
return [
    ...
    'anchorLinks' => true
];
```

Linked page fragments are a slugified version of the Heading string (`Example Title` -> `#example-title`).

You can extend your Markdown Parser with the provided `Njed\Toc\Extensions\CommonMark\TitleAnchorIdExtension` to provide these ids for your heading nodes in your rendered views.

To extend the default Parser for all Markdown fields in your Statamic instance, add it to your `boot` method in the `AppServiceProvider`:

```php
Markdown::addExtension(function () {
    return new \Njed\Toc\Extensions\CommonMark\TitleAnchorIdExtension;
});
```

To only use this Extension on a specific Markdown field, you can create a new Parser and specify it for your Markdown field.

```php
Markdown::extend('special', function ($parser) {
    return $parser
        ->withStatamicDefaults()
        ->addExtension(function () {
            return new \Njed\Toc\Extensions\CommonMark\TitleAnchorIdExtension;
        });
});
```

You can set a custom parser for markdown fields either in the control panel or through the `parser` attribute in your blueprint file:

```yaml
-
    handle: content
    field:
        ...
        type: markdown
        parser: special
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

