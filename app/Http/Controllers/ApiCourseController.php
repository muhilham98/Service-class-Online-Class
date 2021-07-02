<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Review;
use App\Models\Chapter;
use App\Models\StudentCourse;


use Illuminate\Http\Request;

class ApiCourseController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'string|required',
            'category' => 'in:website,mobile,machine learning,internet of things,cyber security,design,soft skills|required',
            'type' => 'in:free,premium|required',
            'level' => 'in:basic,mid,advance|required',
            'price' => 'integer',
            'certificate' => 'boolean|required',
            'description' => 'string'
        ]);

        $data['code'] = substr(md5(microtime()),rand(0,26),5);
        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $course = Course::create($data)->makeVisible(['code']);
        //$course['code'] = $course->code;
        //dd($course);
        return response()->json([
            'status' => 'success',
            'message' => 'create course successfully',
            'data' => $course
        ]);
    }

    public function index(Request $request)
    {
        $courses = Course::query();
        $name = $request->query('name');
        $category = $request->query('category');

        $courses->when($name, function($query) use ($name) {
            return $query->whereRaw("name LIKE '%".strtolower($name)."%'");
        });

        $courses->when($category, function($query) use ($category) {
            return $query->where('category', '=', $category);
        });
       // $courses->except(['code']);
        return response()->json([
            'status' => 'success',
            'message' => 'get courses successfully',
            'data' => $courses->paginate(5)
        ]);
    }

    public function show($id)
    {
        $course = Course::with(['chapters', 'chapters'])
                        ->with('images')
                        ->find($id);
        
        if(!$course){
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $reviews = Review::where('course_id','=', $id)->get()->toArray();
        $teachers = Teacher::where('course_id','=', $id)->get()->toArray();
        //coba
        // for($x = 0; $x < 1; $x++){
        //     echo $reviews[0]['id'];
        // }
        ////coba
        if(count($reviews) > 0){
            $user_ids = array_column($reviews, 'user_id');
            //dd($user_ids);
            $users = getUserbyIds($user_ids);
            //echo "<pre>".print_r($users, 1)."<pre>";
            //dd($reviews);
            if($users['status'] === 'error'){
                $reviews = [];
            }else{
                foreach($reviews as $k => $review){
                    $user_index = array_search($review['user_id'], array_column($users['data'], '_id'));
                    $reviews[$k]['users_data'] = $users['data'][$user_index];
                }
            }
        }

        if(count($teachers) > 0){
            $user_ids = array_column($teachers, 'user_teacher_id');
            //dd($user_ids);
            $users = getUserbyIds($user_ids);
            //echo "<pre>".print_r($users, 1)."<pre>";
            //dd($teachers);
            if($users['status'] === 'error'){
                $teachers = [];
            }else{
                foreach($teachers as $k => $teacher){
                    $user_index = array_search($teacher['user_teacher_id'], array_column($users['data'], '_id'));
                    $teachers[$k]['teachers_data'] = $users['data'][$user_index];
                }
            }
        }

        $lessons = Chapter::where('course_id','=', $id)->withCount('lessons')->get()->toArray();
        $total_lessons = array_sum(array_column($lessons, 'lessons_count'));
        $total_students = StudentCourse::where('course_id','=', $id)->count();
        ///////dd($total_videos);
        $course['total_lessons'] = $total_lessons;
        $course['total_students'] = $total_students;
        $course['teachers'] = $teachers;
        $course['reviews'] = $reviews;
        return response()->json([
            'status' => 'success',
            'message' => 'get detail course successfully',
            'data' => $course
        ]);

    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'string',
            'category' => 'in:website,mobile,machine learning,internet of things,cyber security,design,soft skills',
            'type' => 'in:free,premium',
            'level' => 'in:basic,mid,advance',
            'price' => 'integer',
            'certificate' => 'boolean',
            'description' => 'string',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $course = Course::find($id);
        if(!$course){
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        //return dd($data);
        $course->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'update course successfully',
            'data' => $course
        ]);
    }

    public function delete($id)
    {
        $course = Course::find($id);
        if(!$course){
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ]);
        }

        $course->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'course has been deleted'
        ]);
    }


    public function getCourseCode(Request $request)
    {
        $courses = Course::query();
        $name = $request->query('name');
        $category = $request->query('category');

        $courses->when($name, function($query) use ($name) {
            return $query->whereRaw("name LIKE '%".strtolower($name)."%'");
        });

        $courses->when($category, function($query) use ($category) {
            return $query->where('category', '=', $category);
        });
        //dd( $courses);
       // $courses->except(['code']);
        return response()->json([
            'status' => 'success',
            'message' => 'get courses successfully',
            'data' => $courses->paginate(5)->makeVisible(['code'])
        ]);
    }

    public function updateCourseCode($id)
    {
        $course = Course::find($id);
        if(!$course){
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $data['code'] = substr(md5(microtime()),rand(0,26),5);
        $course->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'update course successfully',
            'data' => $course->makeVisible(['code'])
        ]);
    }
}
