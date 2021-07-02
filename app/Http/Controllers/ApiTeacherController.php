<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Models\Teacher;
use App\Models\Course;

class ApiTeacherController extends Controller
{


    public function create(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'course_id' => 'integer|required',
            'user_teacher_id' => 'string|required',
            'code' => 'string|required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $course_id = $request->input('course_id');
        $course = Course::find($course_id);
        if(!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }
        $user_teacher_id = $request->input('user_teacher_id');
        //echo strlen($user_id);
        if(strlen($user_teacher_id) !== 24){
            return response()->json([
                'status' => 'error',
                'message' => 'id must be 24 character'
            ], 400);
        }

        $user = getUserbyId($user_teacher_id);

       //dd($user);

        if($user['status'] === 'error'){
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['status_code']);
        }
        //dd($user["data"]["role"]);
        // if($user["data"]["role"] !== "teacher"){
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'forbidden or user unauthorized'
        //     ], 403);
        // }
        //echo "OK";

        $is_duplicate = Teacher::where('course_id', '=', $course_id)
                                                ->where('user_teacher_id', '=', $user_teacher_id)
                                                ->exists();
        if($is_duplicate){
            return response()->json([
                'status' => 'error',
                'message' => 'teacher already in this course'
            ], 409);
        }

        $code = $request->input('code');
        if($code !== $course['code']){
            return response()->json([
                'status' => 'error',
                'message' => 'code invalid'
            ], 401);
        }

        $teacher_course = Teacher::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'teacher take this course successfully',
            'data' => $teacher_course 
        ]);
        
    }

    public function index(Request $request)
    {
        $user_teacher_id = $request->query('user_teacher_id');
        //echo gettype($user_id);
        $teachers_courses = Teacher::query()->with('course');

        $teachers_courses->when($user_teacher_id, function($query) use ($user_teacher_id){
            return $query->where('user_teacher_id', '=', $user_teacher_id);
        });

        $data_teachers_courses =  $teachers_courses->get();

        return response()->json([
            'status' => 'success',
            'message' => 'get list successfully',
            'data' =>  $data_teachers_courses
        ]);
    }

    public function show()
    {
        
        $teachers_courses = Teacher::all()->toArray();
        
        if(count($teachers_courses) > 0){
            $user_ids = array_column($teachers_courses, 'user_teacher_id');
            $course_ids = array_column($teachers_courses, 'course_id');
            //dd($user_ids);
            $users = getUserbyIds($user_ids);
            $course = Course::whereIn('id', $course_ids)->get()->toArray();
            //dd($course);
            //echo "<pre>".print_r($users, 1)."<pre>";
            //dd($teachers);
            if($users['status'] === 'error'){
                $teachers_courses = [];
            }else{
                foreach($teachers_courses as $k => $teacher){
                    $user_index = array_search($teacher['user_teacher_id'], array_column($users['data'], '_id'));
                    $course_index = array_search($teacher['course_id'], array_column($course, 'id'));
                    $teachers_courses[$k]['teacher_data'] = $users['data'][$user_index];
                    $teachers_courses[$k]['course_data'] = $course[$course_index];
                }
            }
        }
        //dd($teachers_courses);
        return response()->json([
            'status' => 'success',
            'message' => 'get list successfully',
            'data' =>  $teachers_courses
        ]);
    }


    public function delete($id)
    {
        $teacher = Teacher::find($id);
        if(!$teacher){
            return response()->json([
                'status' => 'error',
                'message' => 'teacher not found'
            ], 404);
        }

        $teacher->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'teacher has been deleted from this course'
        ]);

    }

}
