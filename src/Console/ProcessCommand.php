<?php
namespace Shinedira\ShineSwagger\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use ReflectionMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProcessCommand extends Command
{
    protected $signature = 'generate:api-docs';

    protected $description = 'Creating Api Spesification.';

    public function handle()
    {
        // if (is_null(config('shineSwagger'))) {
        //    return $this->warn('Config not publish properly, please running publish with --tag=shine-swagger-config');
        // }

        $routes = collect(Route::getRoutes())->map(function ($route) {
            if (in_array("api", $route->gatherMiddleware())) {
                return $this->getRouteInformation($route);
            }
        })->filter()->all();

        $test = [
            'openapi' => "3.0.0",
            'info' => [
                'version' => config('shine-swagger.version'),
                'title' => config('shine-swagger.title'),
                'description' => config('shine-swagger.decription'),
            ]
        ];

        foreach ($routes as $route) {
            if (!$methodName = $route['methodName']) continue;

            $method  = strtolower(str_replace('|HEAD', '', $route['method']));
            $tag     = explode("/", $route['uri'])[2];
            $reflect = new ReflectionMethod($route['class'], $route['methodName']);
            $parameters = $reflect->getParameters();
            
            $path = [
                'tags' => [$tag],
                "summary" => $method . ucfirst($tag) . ucfirst($methodName),
                "description" =>  "Find pet by ID",
                "operationId" => $method . ucfirst($tag) . ucfirst($methodName),
                'parameters' => [
                    [
                        'name' => 'Accept',
                        'in' => 'header',
                        'required' => true,
                        'schema' => [
                            'type' => 'string'
                        ]
                    ]
                ]
            ];

            if (in_array('auth:api', $route['middleware'])) {
                $path['parameters'][] = [
                            'name' => 'Authorization',
                            'in' => 'header',
                            'required' => true,
                            'schema' => [
                                'type' => 'string'
                        ]
                    ];
            }

            foreach ($routes as $route) {
                if (!$methodName = $route['methodName']) continue;

                $method  = strtolower(str_replace('|HEAD', '', $route['method']));
                $tag     = explode("/", $route['uri'])[2];
                $reflect = new ReflectionMethod($route['class'], $route['methodName']);
                $parameters = $reflect->getParameters();
                $path = [];
                
                $path = [
                    'tags' => [$tag],
                    "summary" => $method . ucfirst($tag) . ucfirst($methodName),
                    "description" =>  "Find pet by ID",
                    "operationId" => $method . ucfirst($tag) . ucfirst($methodName),
                    'parameters' => [
                        [
                            'name' => 'Accept',
                            'in' => 'header',
                            'required' => true,
                            'schema' => [
                                'type' => 'string'
                            ]
                        ]
                    ]
                ];

                if (in_array('auth:api', $route['middleware'])) {
                    $path['parameters'][] = [
                                'name' => 'Authorization',
                                'in' => 'header',
                                'required' => true,
                                'schema' => [
                                    'type' => 'string'
                            ]
                        ];
                }

                foreach ($parameters as $parameter) {
                    $type = optional($parameter->getType())->getName() ?? null;

                    if (class_exists($type) && ($request = new $type) instanceof Request ) {
                        if (class_exists($type) && $request instanceof FormRequest ) {
                            foreach ($request->rules() as $key => $rule) {

                                $path['requestBody']['description'] = 'Parameter for ' . $method;
                                $path['requestBody']['content']['application/x-www-form-urlencoded']['schema']['properties'][$key] = [
                                    'type' => strpos($rule, 'numeric') ? 'integer' : 'string',
                                ];

                                $path['responses']['200']['description'] = "success";
                                $path['responses']['200']['content']['application/json']['schema']['properties'][$key] = [
                                    'type' => strpos($rule, 'numeric') ? 'integer' : 'string',
                                ];

                            }
                        }
                    } else {
                        $path['parameters'][] = [
                            'name' => $parameter->name,
                            'in'   => 'path',
                            'required' => true,
                            'description' => $type ? (class_exists($type) ? 'instanceof ' . $type : $type) : 'any',
                            'schema' => [
                                'type' => 'string'
                            ]
                        ];
                    }

                }

                if (!isset($path['responses'])) {
                    $path['responses'][200] = [ 'description' => 'Success'];
                }

                $path['responses'][422] = [ 'description' => 'UnprocessableEntity'];
                $path['responses'][404] = [ 'description' => 'Not Found'];

                $test['paths']["/" . $route['uri']][$method] = $path;
            }

        }

        file_put_contents(public_path('shine-swagger/api-spec.json'), json_encode($test));

        $this->info('Success');
    }

    public function getRouteInformation($route)
    {
        $action = ltrim($route->getActionName(), '\\');
        $xplode = explode('@', $action);
        $class  = $xplode[0] ?? null;
        $method = $xplode[1] ?? null;

        return [
            'domain' => $route->domain(),
            'method' => implode('|', $route->methods()),
            'uri'    => $route->uri(),
            'name'   => $route->getName(),
            'action' => $action,
            'class'  => $class,
            'methodName'  => $method,
            'middleware' => $route->gatherMiddleware(),
        ];
    }
}
