## Joligoms/nova-sort-relations

This package improves support for sorting relations in Laravel Nova.

## Installation

Install via composer

``` bash
$ composer require joligoms/nova-sort-relations
```

## Usage

Include `Joligoms\SortRelations\SortRelations` trait to your resource class or in `App\Nova\Resource` if you want all resources to have this feature. Define sortable columns in `$sortRelations` array.

```php

...
use Joligoms\SortRelations\SortRelations;
...

class Post extends Resource
{
    public static $sortRelations = [
        'user' => /* 
                      The attribute specified in the field, for example: 
                                              ˅˅˅˅
                      Text::make(__('User'), 'user', function () {
                          return $this->model()->user->username;
                      });
                   */
            [
                'relation' => 'user', // The relation from the current resource's model.
                'title' => 'username', // (Optional) The relation's column that should be selected in the indexQuery. If not specified, it will use the resource's title property.
                'columns' => 'username' // The columns that should be ordered in the query. It can be a string or array.
            ],
        'company' => [
            'relation' => 'user.company', // Dot notation can be specified when the relation goes through multiple relations.
            'columns' => 'name'
        ]
    ];
    
    public static function indexQuery(NovaRequest $request, $query)
    {
        // You can modify your base query here, only if necessary. Sort Relations will be applied automatically...
        return $query;
    }
}

```


## Security

If you discover any security-related issues, please email the author instead of using the issue tracker.

## Credits 
- [Jani Cerar](https://github.com/janicerar)
- [Adam Anderly](https://github.com/anderly)
- [LifeOnScreen](https://github.com/LifeOnScreen)
- [Newton Evangelista da Gama Junior](https://github.com/newtongamajr)

## License

MIT license. Please see the [license file](docs/license.md) for more information.

<!-- [ico-version]: https://img.shields.io/packagist/v/lifeonscreen/nova-sort-relations.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/lifeonscreen/nova-sort-relations.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/lifeonscreen/nova-sort-relations
[link-downloads]: https://packagist.org/packages/lifeonscreen/nova-sort-relations
[link-author]: https://github.com/joligoms -->
