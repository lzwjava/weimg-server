<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['self']['PATCH'] = 'users/update';
$route['self']['GET'] = 'users/self';
$route['users']['POST'] = 'users/register';
$route['login']['POST'] = 'users/login';
$route['requestSmsCode'] = 'users/requestSmsCode';

$route['files/uptoken']['GET'] = 'files/uptoken';

$route['images']['POST'] = 'images/create';
$route['images/(\w+)']['GET'] = 'images/fetch/$1';

$route['posts']['POST'] = 'posts/create';
$route['posts/(\d+)']['GET'] = 'posts/fetch/$1';
$route['posts']['GET'] = 'posts/list';
$route['posts/(\d+)/vote/(up|down)'] = 'posts/vote/$1/$2';

$route['posts/(\d+)/comments'] = 'comments/create/$1';
$route['posts/(\d+)/comments/(\d+)/vote/(up|down)'] = 'comments/vote/$2/$3';
