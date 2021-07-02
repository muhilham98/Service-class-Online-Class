<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Lesson;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\StudentCourse;
use App\Models\Teacher;

class ApiLessonController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'string|required',
            'video' => 'string|required',
            'description' => 'string',
            'chapter_id' => 'integer|required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $chapter_id = $request->input('chapter_id');
        $chapter = Chapter::find($chapter_id);
        if(!$chapter){
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        $course_id = $chapter['course_id'];
        $course = Course::find($course_id);
        if(!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $user_id = $request->query('user_id');
        if(!$user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'bad request'
            ], 400);
        }

        $user = getUserbyId($user_id);
        if($user['status'] === 'error'){
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['status_code']);
        }
        $user_id = $user['data']['_id'];

        $isTeacherExist = Teacher::where('course_id', '=', $course_id)
                                    ->where('user_teacher_id', '=', $user_id)
                                    ->exists();
        if(!$isTeacherExist){
            return response()->json([
                'status' => 'error',
                'message' => 'forbidden access'
            ], 403);
        }

        $lesson = Lesson::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'create lesson successfully',
            'data' => $lesson
        ]);
    }

    public function index(Request $request)
    {
        $lessons = Lesson::query();

        $chapter_id = $request->query('chapter_id');

        $lessons->when($chapter_id, function($query) use ($chapter_id){
            return $query->where('chapter_id', '=', $chapter_id);
        });

        $data_lessons = $lessons->with('images_lesson')
                        ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'get lessons successfully',
            'data' => $data_lessons
        ]);
    }

    public function show($id, Request $request)
    {
        $user_id = $request->query('user_id');
        if(!$user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'bad request'
            ], 400);
        }

        $user = getUserbyId($user_id);
        if($user['status'] === 'error'){
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['status_code']);
        }
        $user_id = $user['data']['_id'];

        $lesson = Lesson::with('images_lesson')
                    ->find($id);
        if(!$lesson){
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        //dd($lesson['chapter_id']);
        //dd($chapter['course_id']);
        //$user = getUserbyId($user_id);
        $chapter_id = $lesson['chapter_id'];
        $chapter = Chapter::find($chapter_id);
        $course_id = $chapter['course_id'];
        //dd($user['data']['_id']);
        //dd($user['data']['role']);
        if($user['data']['role']==='student'){
            $isStudentExist = StudentCourse::where('course_id', '=', $course_id)
                                    ->where('user_id', '=', $user_id)
                                    ->exists();
            if($isStudentExist){
                return response()->json([
                    'status' => 'success',
                    'message' => 'get a lesson successfully',
                    'data' => $lesson
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'forbidden access'
            ], 403);

        }

        $isTeacherExist = Teacher::where('course_id', '=', $course_id)
                                    ->where('user_teacher_id', '=', $user_id)
                                    ->exists();
        if($isTeacherExist){
            return response()->json([
                'status' => 'success',
                'message' => 'get a lesson successfully',
                'data' => $lesson
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'forbidden access'
        ], 403);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'string',
            'video' => 'string',
            'description' => 'string',
            'chapter_id' => 'integer'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $lesson = Lesson::find($id);
        if(!$lesson){
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        $chapter_id = $request->input('chapter_id');
        if($chapter_id){
            $chapter= Chapter::find($chapter_id);
            if(!$chapter){
                return response()->json([
                    'status' => 'error',
                    'message' => 'chapter not found'
                ], 404);
            }
            $chapter = Chapter::find($chapter_id);
            $course_id = $chapter['course_id'];

            $user_id = $request->query('user_id');
           //dd($request->all());
           if(!$user_id){
               return response()->json([
                   'status' => 'error',
                   'message' => 'bad request'
               ], 400);
           }
   
           $user = getUserbyId($user_id);
           if($user['status'] === 'error'){
               return response()->json([
                   'status' => $user['status'],
                   'message' => $user['message']
               ], $user['status_code']);
           }
           
           //dd($course_id);
           $isTeacherExist = Teacher::where('course_id', '=', $course_id)
                                       ->where('user_teacher_id', '=', $user_id)
                                       ->exists();
           if(!$isTeacherExist){
               return response()->json([
                   'status' => 'error',
                   'message' => 'forbidden access'
               ], 403);
           }
        }

        $lesson->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'update lesson successfully',
            'data' => $lesson
        ]);

    }

    public function delete($id)
    {
        $lesson = Lesson::find($id);
        if(!$lesson){
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        $lesson->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'lesson has been deleted'
        ]);

    }
}
