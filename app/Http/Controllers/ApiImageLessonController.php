<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ImageLesson;
use App\Models\Lesson;

class ApiImageLessonController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'image' => 'url|required',
            'lesson_id' => 'integer|required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $lesson_id = $request->input('lesson_id');
        $lesson = Lesson::find($lesson_id);
        if(!$lesson){
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        $image_lesson = ImageLesson::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'image lesson url added',
            'data' => $image_lesson
        ]);
    }

    
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'image' => 'url',
            'lesson_id' => 'integer'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $image_lesson = ImageLesson::find($id);
        if(!$image_lesson){
            return response()->json([
                'status' => 'error',
                'message' => 'image lesson not found'
            ], 404);
        }

        $lesson_id = $request->input('lesson_id');
        if($lesson_id){
            $lesson= Lesson::find($lesson_id);
            if(!$lesson){
                return response()->json([
                    'status' => 'error',
                    'message' => 'lesson not found'
                ], 404);
            }
        }

        $image_lesson->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'update image lesson attribute successfully',
            'data' => $image_lesson
        ]);

    }

    public function delete($id)
    {
        $image_lesson = ImageLesson::find($id);
        if(!$image_lesson ){
            return response()->json([
                'status' => 'error',
                'message' => 'image lesson not found'
            ], 404);
        }

        $image_lesson->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'image lesson has been deleted'
        ]);

    }
}
