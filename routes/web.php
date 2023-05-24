<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
$router->post('/admin/login', 'UserController@login');

$router->group(['middleware' => ['auth']], function() use($router) {
    
    $router->group(['middleware' => ['school']], function() use($router) {
        $router->get('/school/getSelf', 'SchoolController@getSchoolLogin');
        $router->post('/school/logout', 'SchoolController@logout');
        $router->put('/school/updatepassword/{id}', 'SchoolController@updatePassword');
        $router->put('/school/data/{id}/update', 'DataController@updateSchool');
        $router->get('/getStudentTeacherCount', 'SchoolController@getStudentTeacherCount');
        $router->get('/getStudents', 'SchoolStudentController@getStudents');
        $router->get('/getStudentsYear', 'SchoolStudentController@getStudentsYear');
        $router->post('/storeStudents', 'SchoolStudentController@storeStudents');
        $router->get('/getTeachers', 'SchoolTeacherController@getTeachers');
        $router->get('/getTeachersYear', 'SchoolTeacherController@getTeachersYear');
        $router->post('/storeTeachers', 'SchoolTeacherController@storeTeachers');
    });
    
    $router->group(['middleware' => ['supervisor']], function() use($router) {
        $router->post('/supervisor/logout', 'SupervisorController@logout');
        $router->get('/supervisor/getSelf', 'SupervisorController@getSelf');
        $router->get('/supervisor/getStudentsYear/{id}', 'SchoolStudentController@getStudentsYearSupervisor');
        $router->get('/supervisor/getSchoolBySupervisor/{id}', 'SupervisorController@getSchoolBySupervisor');
        $router->put('/supervisor/updatepassword/{id}', 'SupervisorController@updatePassword');
        $router->get('/supervisor/getData', 'DataController@getDataBySupervisor');
        $router->post('/supervisor/verifyData', 'DataController@verifyData');
        $router->post('/supervisor/revisionData', 'DataController@revisionData');
    });
    
    $router->group(['middleware' => ['diknas']], function() use($router) {
        $router->post('/diknas/logout', 'DiknasController@logout');
        $router->get('/diknas/getSelf', 'DiknasController@getSelf');
        $router->put('/diknas/updatepassword/{id}', 'DiknasController@updatePassword');
    });

    $router->group(['middleware' => ['admin']], function() use($router) {
        $router->post('/admin/logout', 'UserController@logout');
        $router->get('/admin', 'UserController@index');
        $router->get('/admin/getSelf', 'UserController@getSelf');
        $router->get('/admin/countUsers', 'UserController@countUsers');
        $router->post('/admin/create', 'UserController@store');
        $router->put('/admin/update/{id}', 'UserController@update');
        $router->delete('/admin/delete/{id}', 'UserController@delete');
        $router->put('/admin/updatepassword/{id}', 'UserController@updatepassword');
        $router->put('/data/update/{id}', 'DataController@update');
        $router->delete('/data/delete/{id}', 'DataController@delete');
        $router->delete('/school/delete/{id}', 'SchoolController@destroy');
        $router->delete('/supervisor/delete/{id}', 'SupervisorController@delete');
        $router->delete('/diknas/delete/{id}', 'DiknasController@delete');
    });

    $router->get('/school', 'SchoolController@index');
    $router->post('/school/create', 'SchoolController@store');
    $router->get('/school/get/{id}', 'SchoolController@getSingle');
    $router->put('/school/update/{id}', 'SchoolController@update');
    $router->get('/countSchool', 'SchoolController@countSchool');
    $router->get('/getRevision', 'RevisionController@getRevision');
    $router->get('/getAllSchool', 'SchoolController@getAllSchool');

    $router->get('/supervisor', 'SupervisorController@index');
    $router->get('/supervisor/getAll', 'SupervisorController@getAll');
    $router->post('/supervisor/create', 'SupervisorController@store');
    $router->put('/supervisor/update/{id}', 'SupervisorController@update');
    $router->get('/getSchoolSupervisorCount', 'DiknasController@getSchoolSupervisorCount');

    $router->get('/diknas', 'DiknasController@index');
    $router->post('/diknas/create', 'DiknasController@store');
    $router->put('/diknas/update/{id}', 'DiknasController@update');
    
    $router->get('/getSchoolStats', 'DiknasController@getSchoolStats');
    $router->get('/getStudentTeacherYearList', 'DiknasController@getStudentTeacherYearList');
    $router->get('/searchSchoolFilter', 'DataController@searchSchoolFilter');
    $router->get('/getCategories', 'CategoryController@index');
    $router->get('/getDataTypes', 'CategoryController@getDataTypes');
    $router->get('/getStatus', 'StatusController@index');
    $router->post('/downloadFile', 'DataController@downloadFile');
    $router->get('/getData', 'DataController@index');
    $router->get('/getData/{id}', 'DataController@getDataById');
    $router->post('/data/create', 'DataController@create');
    $router->get('/getSchoolType', 'SchoolController@getSchoolType');
    $router->get('/getDataYear', 'DataController@getDataYear');
});