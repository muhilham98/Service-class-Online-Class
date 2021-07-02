<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use App\Models\StudentCourse;

class ApiStudentCourseController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'course_id' => 'integer|required',
            'user_id' => 'string|required'
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

        $user_id = $request->input('user_id');
        //echo strlen($user_id);
        if(strlen($user_id) !== 24){
            return response()->json([
                'status' => 'error',
                'message' => 'user_id must be 24 character'
            ], 400);
        }

        $user = getUserbyId($user_id);

        //dd($user);

        if($user['status'] === 'error'){
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['status_code']);
        }
        //dd($user['data']['_id']);

        $is_duplicate = StudentCourse::where('course_id', '=', $course_id)
                                                ->where('user_id', '=', $user_id)
                                                ->exists();
        if($is_duplicate){
            return response()->json([
                'status' => 'error',
                'message' => 'user has already registered in this course'
            ], 409);
        }

        if($course->toArray()['type'] === 'free'){
            $student_course = StudentCourse::create($data);
            return response()->json([
                'status' => 'success',
                'message' => 'join free course successfully',
                'data' => $student_course
            ]);
        }
        
        $order = orderPremium([
            'user' => $user['data'],
            'course' => $course->toArray()
        ]);
        //dd($order);
        if($order['status'] === 'error'){
            return response()->json([
                'status' =>  $order['status'],
                'message' => $order['message']
            ], $order['status_code']);
        }

        //$student_course = StudentCourse::create($data);
        return response()->json([
            'status' =>  $order['status'],
            'message' => $order['message'],
            'data'=> $order['data']
        ]);
    }

    public function premiumAccess(Request $request)
    {
        $data =  $request->all();
        $student_course = StudentCourse::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'join premium class successfully',
            'data' => $student_course
        ]);
    }

    public function index(Request $request)
    {
        $user_id = $request->query('user_id');
        //echo gettype($user_id);
        $students_courses = StudentCourse::query()->with('course');

        $students_courses->when($user_id, function($query) use ($user_id){
            return $query->where('user_id', '=', $user_id);
        });

        $data_students_courses =  $students_courses->get();

        return response()->json([
            'status' => 'success',
            'message' => 'get list successfully',
            'data' => $data_students_courses
        ]);
    }

}
