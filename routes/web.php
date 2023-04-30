<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\SchoolController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/school/login', 'SchoolController@login');
$router->post('/supervisor/login', 'SupervisorController@login');
$router->post('/diknas/login', 'DiknasController@login');

$router->group(['middleware' => 'auth'], function() use($router) {
    $router->get('/school', 'SchoolController@index');
    $router->post('/school', 'SchoolController@store');
    $router->get('/school/getSelf', 'SchoolController@getSchoolLogin');
    $router->get('/school/get/{id}', 'SchoolController@getSingle');
    $router->put('/school/update/{id}', 'SchoolController@update');
    $router->post('/school/logout', 'SchoolController@logout');
    $router->get('/school/getData/{id}', 'DataController@getDataBySchool');
    $router->put('/school/data/{id}/update', 'DataController@updateSchool');
    $router->put('/school/updatepassword/{id}', 'SchoolController@updatePassword');
    $router->get('/school/getStudents/{id}', 'SchoolStudentController@getStudents');
    $router->get('/school/getStudentsYear/{id}', 'SchoolStudentController@getStudentsYear');
    $router->post('/school/students', 'SchoolStudentController@storeStudents');
    $router->get('/school/getTeachers/{id}', 'SchoolTeacherController@getTeachers');
    $router->get('/school/getTeachersYear/{id}', 'SchoolTeacherController@getTeachersYear');
    $router->post('/school/teachers', 'SchoolTeacherController@storeTeachers');
    $router->get('/school/getRevisionData/{id}', 'RevisionController@getRevisionData');

    $router->post('/supervisor/logout', 'SupervisorController@logout');
    $router->get('/supervisor/getSelf', 'SupervisorController@getSelf');
    $router->get('/supervisor/getData/{id}', 'DataController@getDataBySupervisor');
    $router->get('/supervisor/getSchoolBySupervisor/{id}', 'SupervisorController@getSchoolBySupervisor');
    $router->get('/supervisor/getPaginatedSchoolBySupervisor/{id}', 'SupervisorController@getPaginatedSchoolBySupervisor');
    $router->post('/supervisor/verifyData', 'DataController@verifyData');
    $router->post('/supervisor/revisionData', 'DataController@revisionData');
    $router->put('/supervisor/updatepassword/{id}', 'SupervisorController@updatePassword');

    $router->get('/diknas/getSelf', 'DiknasController@getSelf');
    $router->get('/diknas/getAllSchool', 'DiknasController@getAllSchool');
    $router->get('/diknas/getData', 'DataController@getVerifiedData');
    $router->get('/diknas/getSchoolStats', 'DiknasController@getSchoolStats');
    $router->put('/diknas/updatepassword/{id}', 'DiknasController@updatePassword');
    
    $router->get('/getCategories', 'CategoryController@index');
    $router->get('/getDataTypes', 'CategoryController@getDataTypes');
    $router->get('/getStatus', 'StatusController@index');
    $router->post('/downloadFile', 'DataController@downloadFile');
    $router->get('/edit/{id}', 'DataController@edit');
    $router->get('/getData', 'DataController@index');
    $router->get('/getDataById/{id}', 'DataController@getDataById');
    $router->post('/data/create', 'DataController@create');
    $router->delete('/data/{id}/delete', 'DataController@delete');
});