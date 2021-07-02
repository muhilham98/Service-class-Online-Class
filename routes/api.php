<?php

//namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


//end-point courses
Route::post('courses', 'ApiCourseController@create');
Route::put('courses/{id}', 'ApiCourseController@update');
Route::get('courses', 'ApiCourseController@index');
Route::delete('courses/{id}', 'ApiCourseController@delete');
Route::get('courses/{id}', 'ApiCourseController@show');
Route::get('courses_code', 'ApiCourseController@getCourseCode');
Route::put('courses_code/{id}', 'ApiCourseController@updateCourseCode');


//end-point teachers
Route::post('teachers', 'ApiTeacherController@create');
Route::get('teachers', 'ApiTeacherController@index');
Route::delete('teachers/{id}', 'ApiTeacherController@delete');
Route::get('teachers/all', 'ApiTeacherController@show');

//end-point chapters
Route::post('chapters', 'ApiChapterController@create');
Route::put('chapters/{id}', 'ApiChapterController@update');
Route::get('chapters', 'ApiChapterController@index');
Route::get('chapters/{id}', 'ApiChapterController@show');
Route::delete('chapters/{id}', 'ApiChapterController@delete');

//end-point lessons
Route::post('lessons', 'ApiLessonController@create');
Route::put('lessons/{id}', 'ApiLessonController@update');
Route::get('lessons', 'ApiLessonController@index');
Route::get('lessons/{id}', 'ApiLessonController@show');
Route::delete('lessons/{id}', 'ApiLessonController@delete');

//endpoint file
Route::post('files', 'ApiFileController@create');
Route::put('files/{id}', 'ApiFileController@update');
Route::get('files', 'ApiFileController@index');
Route::get('files/{id}', 'ApiFileController@show');
Route::delete('files/{id}', 'ApiFileController@delete');

//endpoint image lesson
Route::post('images-lessons', 'ApiImageLessonController@create');
Route::put('images-lessons/{id}', 'ApiImageLessonController@update');
Route::delete('images-lessons/{id}', 'ApiImageLessonController@delete');

//end-point ImageCourse
Route::post('images-courses', 'ApiImageCourseController@create');
Route::put('images-courses/{id}', 'ApiImageCourseController@update');
Route::delete('images-courses/{id}', 'ApiImageCourseController@delete');

//end-point StudentCourse
Route::post('students-courses', 'ApiStudentCourseController@create');
Route::post('students-courses/premium-access', 'ApiStudentCourseController@premiumAccess');
Route::get('students-courses', 'ApiStudentCourseController@index');

//end-point Review
Route::post('reviews', 'ApiReviewCourseController@create');
Route::get('reviews', 'ApiReviewCourseController@index');
Route::put('reviews/{id}', 'ApiReviewCourseController@update');
Route::delete('reviews/{id}', 'ApiReviewCourseController@delete');



