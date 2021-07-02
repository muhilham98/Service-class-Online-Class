<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\StudentCourse;
use App\Models\Teacher;

class ApiChapterController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'string|required',
            'course_id' => 'integer|required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        //dd($data);

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
        $course_id = $request->input('course_id');
        $course = Course::find($course_id);
        if(!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $isTeacherExist = Teacher::where('course_id', '=', $course_id)
                                    ->where('user_teacher_id', '=', $user_id)
                                    ->exists();
        if(!$isTeacherExist){
            return response()->json([
                'status' => 'error',
                'message' => 'forbidden access'
            ], 403);
        }


        $chapter = Chapter::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'create chapter successfully',
            'data' => $chapter
        ]);
    }

    public function index(Request $request)
    {
        $chapters = Chapter::query();
        $course_id = $request->query('course_id');

        $chapters->when($course_id, function($query) use ($course_id){
            return $query->where('course_id', '=', $course_id);
        });

        $data_chapters = $chapters->get();

        return response()->json([
            'status' => 'success',
            'message' => 'get chapters successfully',
            'data' => $data_chapters
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

        $chapter = Chapter::with(['lessons', 'files'])
                            ->find($id);
        if(!$chapter){
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        $course_id = $chapter['course_id'];
        //dd($course_id);

        if($user['data']['role']==='student'){
            $isStudentExist = StudentCourse::where('course_id', '=', $course_id)
                                    ->where('user_id', '=', $user_id)
                                    ->exists();
            if($isStudentExist){
                return response()->json([
                    'status' => 'success',
                    'message' => 'get a chapter successfully',
                    'data' => $chapter
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
                'message' => 'get a chapter successfully',
                'data' => $chapter
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
            'course_id' => 'integer'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        //echo gettype($id);
        $chapter = Chapter::find($id);
        if(!$chapter){
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        $course_id = $request->input('course_id');
        //dd($course_id);
        if($course_id){
           $course = Course::find($course_id);
           if(!$course){
                return response()->json([
                    'status' => 'error',
                    'message' => 'course not found'
                ], 404);
           }
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


        $chapter->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'update chapter successfully',
            'data' => $chapter
        ]);

    }

    public function delete($id)
    {
        $chapter = Chapter::find($id);
        if(!$chapter){
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        $chapter->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'chapter has been deleted'
        ]);

    }

    
}
