<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Transformers\CourseTransformer;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseVideo;
use App\Models\CourseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoursesController extends Controller
{
    public function index(Request $request)
    {
        $category_id = intval($request['c']);
        $tag = strval($request['t']);
        if (!$category_id && !$tag) {
            return $this->errorBadRequest();
        }
        //DB::connection()->enableQueryLog(); // 开启查询日志
        if($category_id){
            $categories = Category::where('parent_id', $category_id)->get();
            $collection = collect($categories);
            $collection = $collection->pluck(['id']);
            $collection->push($category_id);
            $courses = Course::whereIn('category_id', $collection)->paginate(per_page());
        }

        if($tag){
            $courses = Course::whereHas('tags', function($query){
                $query->where('name', '=', '热门课程');
            })->paginate(per_page());
        }
        //print_r(DB::getQueryLog());exit();
        return $this->response()->paginator($courses, new CourseTransformer());

    }

    public function show($id)
    {
        $course = Course::findOrFail($id);

        // 是否已购买该课程
        if ($user_id = auth('api')->id()){
            $bought = Course::where('id','=',$id)
                ->whereHas('order', function($q) use ($user_id){
                    $q->where('user_id', '=', $user_id);
                    $q->Where('status','=','completed');
                })->exists();
            $course->bought = $bought;
        }

        return $this->response()->item($course, new CourseTransformer());
    }
}