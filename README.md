# ShineSwagger

## Instalation 
### Install Package Via Composer
`composer require shinedira/shine-swagger`

Add to `config.app`
```
'providers' => [
    Shinedira\ShineSwagger\ShineSwaggerProvider::class,
]
```

Publish config and asset
```
php artisan vendor:publish --tag="shine-swagger"
```

Run command
```
php artisan generate:api-docs
```

### see the result on http://base_url/api/docs

