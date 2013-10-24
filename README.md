Zend Framework 2 module for drop-in self-hosted comments.

#### Installation

1. Add the module key to your `composer.json` file
```json
    {
        "require": {
            "robertboloc/rbcomment": "dev-master"
        }
    }
```

2. Run `composer update`

3. Import the schema from `data/schema.sql` into your database.

4. Add the new module to your application's modules list in `config/application.config.php`
```php
    'modules' => array(
        'Application',
        'RbComment',
    ),
```

#### Usage

In your views use the `rbComment` helper to display the count, the list and a form for adding new comments. Invoke it
where you want your comments box to appear. Simple isn't it? This helper can be used in any view.

```php
<?php echo $this->rbComment($theme) ?>
```
The `$theme` parameter is used to specify the theme of the comments box (if none is specified `default` is used).

Currently, the module is designed to allow only one comment box per page, as it uses
the page uri to identify a thread.

#### Themes

The module comes with 2 themes for now. To implement new ones create a new partial view in `view/theme/yourtheme` using
as base the existing ones.

Use your new theme calling `$this->rbComment('yourtheme')`

The current themes (and possible values of the `$theme` parameter) are :

##### default
Basic theme with no external dependencies. Contains the minimum styling to make it look decent.

##### uikit
This theme requires the [UIkit](http://www.getuikit.com/) CSS framework. If you use it in your project this theme
will make your comments box look awesome.

#### Configuration
The configuration of the module can be found in `config/module.config.php`. Currently the configurable parameters are:

##### default_visibility
This parameter controls the visibility of the newly published comments. If set to 1 all new published comments will be
visible. If 0 they will not be shown. This is useful for moderation.
