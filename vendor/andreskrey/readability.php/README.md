# Readability.php
[![Latest Stable Version](https://poser.pugx.org/andreskrey/readability.php/v/stable)](https://packagist.org/packages/andreskrey/readability.php) [![StyleCI](https://styleci.io/repos/71042668/shield?branch=master)](https://styleci.io/repos/71042668) [![Build Status](https://travis-ci.org/andreskrey/readability.php.svg?branch=master)](https://travis-ci.org/andreskrey/readability.php) [![Total Downloads](https://poser.pugx.org/andreskrey/readability.php/downloads)](https://packagist.org/packages/andreskrey/readability.php) [![Monthly Downloads](https://poser.pugx.org/andreskrey/readability.php/d/monthly)](https://packagist.org/packages/andreskrey/readability.php)

PHP port of *Mozilla's* **[Readability.js](https://github.com/mozilla/readability)**. Parses html text (usually news and other articles) and returns **title**, **author**, **main image** and **text content** without nav bars, ads, footers, or anything that isn't the main body of the text. Analyzes each node, gives them a score, and determines what's relevant and what can be discarded.

![Screenshot](https://raw.githubusercontent.com/andreskrey/readability.php/assets/screenshot.png)

The project aim is to be a 1 to 1 port of Mozilla's version and to follow closely all changes introduced there, but there are some major differences on the structure. Most of the code is a 1:1 copy –even the comments were imported– but some functions and structures were adapted to suit better the PHP language.

## Requirements

PHP 5.6+, ext-dom, ext-xml, and ext-mbstring. To install all this dependencies (in the rare case your system does not have them already), you could try something like this in *nix like environments:

`$ sudo apt-get install php7.1-xml php7.1-mbstring`

**Lead Developer**: Andres Rey

## How to use it

First you have to require the library using composer:

`composer require andreskrey/readability.php`

Then, create a Readability class and pass a Configuration class, feed the `parse()` function with your HTML and echo the variable:

```php 
use andreskrey\Readability\HTMLParser;
use andreskrey\Readability\Configuration;

$readability = new Readability(new Configuration());

$html = file_get_contents('http://your.favorite.newspaper/article.html');

try {
    $readability->parse($html);
    echo $readability;
} catch (ParseException $e) {
    echo sprintf('Error processing text: %s', $e->getMessage);
}
```

Your script will output the parsed text or inform about any errors. You should always wrap the `->parse` call in a try/catch block because if the HTML cannot be parsed correctly, a `ParseException` will be thrown.

If you want to have a finer control on the output, just call the properties one by one, wrapping it with your own HTML.

```php
<h1><?= $readability->getTitle(); ?></h1>
<h2>By <?= $readability->getAuthor(); ?></h2>
<div class="content"><?= $readability->getContent(); ?></div>

```

Here's a list of the available properties:

- Article title: `->getTitle();`
- Article content: `->getContent();`
- Excerpt: `->getExcerpt();`
- Main image: `->getImage();`
- All images: `->getImages();`
- Author: `->getAuthor();`
- Text direction (ltr or rtl): `->getDirection();`

## Options

You can change the behaviour of Readability via the Configuration object. For example, if you want to fix relative URLs and declare the original URL, you could set up the configuration like this:

```php
$configuration = new Configuration();
$configuration->setFixRelativeURLs(true)
    ->setOriginalURL('http://my.newspaper.url/article/something-interesting-to-read.html');
```

Then you pass this Configuration object to Readability. The following options are available. Remember to prepend `set` when calling them.

- **MaxTopCandidates**: default value `5`, max amount of top level candidates.
- **WordThreshold**: default value `500`, minimum amount of characters to consider that the article was parsed successful.
- **ArticleByLine**: default value `false`, search for the article byline and remove it from the text. It will be moved to the article metadata. 
- **StripUnlikelyCandidates**: default value `true`, remove nodes that are unlikely to have relevant information. Useful for debugging or parsing complex or non-standard articles. 
- **CleanConditionally**: default value `true`, remove certain nodes after parsing to return a cleaner result. 
- **WeightClasses**: default value `true`, weight classes during the rating phase. 
- **RemoveReadabilityTags**: default value `true`, remove the data-readability tags inside the nodes that are added during the rating phase. 
- **FixRelativeURLs**: default value `false`, convert relative URLs to absolute. Like `/test` to `http://host/test`. 
- **SubstituteEntities**: default value `false`, disables the `substituteEntities` flag of libxml. Will avoid substituting HTML entities. Like `&aacute;` to á.
- **NormalizeEntities**: default value `false`, converts UTF-8 characters to its HTML Entity equivalent. Useful to parse HTML with mixed encoding.
- **OriginalURL**: default value `http://fakehost`, original URL from the article used to fix relative URLs.
- **SummonCthulhu**: default value `false`, remove all `<script>` nodes via regex. This is not ideal as it might break things, but might be the only solution to [libxml problems with unescaped javascript](https://github.com/andreskrey/readability.php#known-issues). If you're not parsing Javascript tutorials, it's recommended to always set this option as `true`.

## Limitations

Of course the main limitation is PHP. Websites that load the content through lazy loading, AJAX, or any type of javascript fueled call will be ignored (actually, *not ran*) and the resulting text will be incorrect, compared to the readability.js results. All the articles you want to parse with readability.php need to be complete and all the content should be in the HTML already.  

## Known Issues

### Javascript spilling into the text body

DOMDocument has some issues while parsing javascript with unescaped HTML on strings. Consider the following code:

```html
<div> <!-- Offending div without closing tag -->
<script type="text/javascript">
       var test = '</div>';
       // I should not appear on the result
</script>
```

If you would like to remove the scripts of the HTML (like readability does), you would expect ending up with just one div and one comment on the final HTML. The problem is that libxml takes that closing div tag inside the javascript string as a HTML tag, effectively closing the unclosed tag and leaving the rest of the javascript as a string within a P tag. If you save that node, the final HTML will end up like this:

```html
<div> <!-- Offending div without closing tag -->
<p>';
       // I should not appear on the result
</p></div>
```

This is a libxml issue and not a Readability.php bug.

There's a workaround for this: using the `summonCthulhu` option. This will remove all script tags **via regex**, which is not ideal because you may end up summoning [the lord of darkness](https://stackoverflow.com/a/1732454).

### &nbsp entities disappearing

`&nbsp` entities are converted to spaces automatically by libxml and there's no way to disable it.

### Self closing tags rendering as fully expanded tags

Self closing tags like `<br />` get automatically expanded to `<br></br`. No way to disable it in libxml.

## Dependencies

Readability.php has no dependencies to other libraries.

## To-do

- Keep up with Readability.js changes
- Add a small template engine for the __toString() method, instead of using a hardcoded one.

## How it works

Readability parses all the text with DOMDocument, scans the text nodes and gives the a score, based on the amount of words, links and type of element. Then it selects the highest scoring element and creates a new DOMDocument with all its siblings. Each sibling is scored to discard useless elements, like nav bars, empty nodes, etc.

## Code porting

Up to date with readability.js as of [16 Oct 2017](https://github.com/mozilla/readability/commit/c3ff1a2d2c94c1db257b2c9aa88a4b8fbeb221c5).
 
## License

Based on Arc90's readability.js (1.7.1) script available at: http://code.google.com/p/arc90labs-readability

    Copyright (c) 2010 Arc90 Inc

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.