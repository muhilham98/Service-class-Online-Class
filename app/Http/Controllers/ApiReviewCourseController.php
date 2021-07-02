<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;

class ApiReviewCourseController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'course_id' => 'integer|required',
            'user_id' => 'string|required',
            'note_review' => 'string|required'
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
        if(strlen($user_id) !== 24){
            return response()->json([
                'status' => 'error',
                'message' => 'user_id must be 24 character'
            ], 400);
        }

        $user = getUserbyId($user_id);
        if($user['status'] === 'error'){
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message']
            ], $user['status_code']);
        }

        $isReviewExist = Review::where('course_id', '=', $course_id)
                                        ->where('user_id', '=', $user_id)
                                        ->exists();
        if($isReviewExist){
            return response()->json([
                'status' => 'error',
                'message' => 'user has already reviewed this course'
            ], 409);
        }

        $review = Review::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'create review successfully',
            'data' => $review
        ]);

    }

    public function update(Request $request, $id)
    {
        $data = $request->except('course_id', 'user_id');
        $validator = Validator::make($data, [
            'note_review' => 'string'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $review = Review::find($id);
        if(!$review){
            return response()->json([
                'status' => 'error',
                'message' => 'review not found'
            ], 404);
        }

        $review->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'update review successfully',
            'data' => $review
        ]);
    }

    public function index(Request $request)
    {
        $reviews = Review::query();
        $course_id = $request->query('course_id');

        // $courses->when($name, function($query) use ($name) {
        //     return $query->whereRaw("name LIKE '%".strtolower($name)."%'");
        // });

        $reviews->when($course_id, function($query) use ($course_id) {
            return $query->where('course_id', '=', $course_id);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'get courses successfully',
            'data' => $reviews->paginate(5)
        ]);
    }

    public function delete($id)
    {
        $review = Review::find($id);
        if(!$review){
            return response()->json([
                'status' => 'error',
                'message' => 'review not found'
            ], 404);
        }

        $review->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'review has been deleted'
        ]);
    }
}
