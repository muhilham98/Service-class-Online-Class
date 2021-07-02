<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\File;
use App\Models\Chapter;

class ApiFileController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'string|required',
            'file' => 'url|required',
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

        $file = File::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'file added',
            'data' => $file
        ]);
    }

    public function index(Request $request)
    {
        $files = File::query();

        $chapter_id = $request->query('chapter_id');

        $files->when($chapter_id, function($query) use ($chapter_id){
            return $query->where('chapter_id', '=', $chapter_id);
        });

        $data_files = $files->get();

        return response()->json([
            'status' => 'success',
            'message' => 'get files successfully',
            'data' => $data_files
        ]);
    }

    public function show($id)
    {
        $file = File::find($id);
        if(!$file){
            return response()->json([
                'status' => 'error',
                'message' => 'file not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'get a file successfully',
            'data' => $file 
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'string',
            'file' => 'url',
            'description' => 'string',
            'chapter_id' => 'integer'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator ->errors()
            ], 400);
        }

        $file = File::find($id);
        if(!$file){
            return response()->json([
                'status' => 'error',
                'message' => 'file not found'
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
        }

        $file->update($data);
        return response()->json([
            'status' => 'success',
            'message' => 'update file attribute successfully',
            'data' => $file
        ]);

    }

    public function delete($id)
    {
        $file = File::find($id);
        if(!$file){
            return response()->json([
                'status' => 'error',
                'message' => 'file not found'
            ], 404);
        }

        $file->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'file has been deleted'
        ]);

    }

}
