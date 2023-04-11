#### Welcome to the unofficial Tom Petty Buried Treasure site.
This is the repository for the [Tom Petty Buried Treasure Playlists](http://buriedtreasure.phpfui.com) site.

The site was created based off the playlists from [Tom Petty's Buried Treasure Show](https://www.siriusxm.com/channels/tom-pettys-buried-treasure) that airs on Sirius XM Radio. The data was scrapped from [Tom Petty](https://www.tompetty.com) and cleaned up to the form you see here.

Playlists can not be copyrighted, and the data on this site should be considered in the public domain. This site is not associated with Tom Petty's estate or heirs.

## Requirements to run the website
* PHP 8.1 or better
* SQLite 3
* Apache
* Linux or Windows

## Installation
```
git clone git@github.com:phpfui/TomPettyBuriedTreasure.git
```
Set your public root to TomPettyBuriedTreasure/www
No need to run composer, all needed file are checked into the repo

## Configuration
You can edit the data locally if you add a file named Admin.php to the /config directory.  It should contain the following PHP code:
```php
<?php
return [
  'allowAdmin' => true,
];
```

## Corrections
If you find a bug in the PHP code, please submit an issue or Pull Request on GitHub.  If you find a problem with the data, please report it on the [site directly](http://buriedtreasure.phpfui.com/ContactUs).

## License
The PHP code in the TomPettyBuriedTreasure repo is distributed under the MIT License.


