<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImageCourse;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class ApiImageCourseController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'image' => 'url|required',
            'course_id' => 'integer|required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $course_id = $request->input('course_id');
        $course = Course::find($course_id);
        if(!$course){
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $image_course = ImageCourse::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'create image successfully',
            'data' => $image_course
        ]);
    }


    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'image' => 'url',
            'course_id' => 'integer'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $image_course = ImageCourse::find($id);
        if(!$image_course){
            return response()->json([
                'status' => 'error',
                'message' => 'image course not found'
            ], 404);
        }

        $course_id = $request->input('course_id');
        if($course_id){
           $course = Course::find($course_id);
           if(!$course){
                return response()->json([
                    'status' => 'error',
                    'message' => 'course not found'
                ], 404);
           }
        }

        $image_course->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'update image successfully',
            'data' => $image_course
        ]);
    }

    public function delete($id)
    {
        $image_course = ImageCourse::find($id);
        if(!$image_course){
            return response()->json([
                'status' => 'error',
                'message' => 'image course not found'
            ], 404);
        }

        $image_course->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'image course has been deleted'
        ]);
    }
}
