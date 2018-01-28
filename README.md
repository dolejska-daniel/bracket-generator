# PHP Bracket Generator

> Version v0.2

# Table of Contents

1. [Introduction](#introduction)
2. [Downloading](#downloading)
3. [Usage example](#usage-example)

# [Introduction](https://github.com/dolejska-daniel/bracket-generator/wiki/Home#introduction)

Welcome to bracket generator for PHP7 repo. This library uses HTML tables to render nice and
clean game brackets. You can create brackets based on participant count, match list or even
custom tree. Bracket visuals are also completly customizable.

Please, refer mainly to the [wiki pages](https://github.com/dolejska-daniel/bracket-generator/wiki).

# [Downloading](https://github.com/dolejska-daniel/bracket-generator/wiki/Home#downloading)

The easiest way to get this library is to use [Composer](https://getcomposer.org/). While
having Composer installed it takes only `composer require dolejska-daniel/bracket-generator`
and `composer update` to get the library up and ready!

If you are not fan of Composer, you can download [whole repository in .zip archive](https://github.com/dolejska-daniel/bracket-generator/archive/master.zip)
or clone the repository using Git - `git clone https://github.com/dolejska-daniel/bracket-generator`.
_But in this case, you will have to create your own autoload function._

# [Usage example](https://github.com/dolejska-daniel/bracket-generator/wiki/Usage-example)

All it takes to create a bracket is this:

```php
use BracketGenerator\Bracket;
echo Bracket::create(8);
```

And what will you end up with after this? Take a look:

![Empty bracket for 8 participants](https://image.prntscr.com/image/V95bQ0atR8GGymk_s4_fng.png)

It looks a bit empty so you can fill it up very easily - let's start from the beginning:

```php
use BracketGenerator\Bracket;

$bracket = Bracket::create(8);
$bracket->fillByParticipantList([
	[ 'Participant 1', 0 ],
	[ 'Participant 2', 0 ],
	[ 'Participant 3', 0 ],
	[ 'Participant 4', 0 ],
	[ 'Participant 5', 0 ],
	[ 'Participant 6', 0 ],
	[ 'Participant 7', 0 ],
	[ 'Participant 8', 0 ],
]);

echo $bracket;
```

And that gives us:

![Filled bracket for 8 participants](https://image.prntscr.com/image/qzV6ch6sRJGeWKVjPJuvLw.png)

Please remember that the bracket visuals are completly customizable. This is just an example.

For more, please see [the wiki pages](https://github.com/dolejska-daniel/bracket-generator/wiki/Usage-example).