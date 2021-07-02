<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
    protected $hidden = ['code'];

    protected $fillable =[
        'name', 'category', 'type', 'level', 'price', 
        'certificate', 'description', 'code'
    ];

    public function chapters()
    {
        return $this->hasMany('App\Models\Chapter')->orderBy('id', 'ASC');
    }

    public function images()
    {
        return $this->hasMany('App\Models\ImageCourse')->orderBy('id', 'ASC');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review')->orderBy('id', 'DESC');
    }

    public function students()
    {
        return $this->hasMany('App\Models\StudentCourse')->orderBy('id', 'ASC');
    }

    // public function teacher()
    // {
    //     return $this->belongsTo('App\Models\Teacher');
    // }
}
