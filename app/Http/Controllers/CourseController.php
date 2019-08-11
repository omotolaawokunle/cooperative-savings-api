<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\CoursesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Jobs\CreateCourseJob;
use JWTAuth;
use App\Course;

class CourseController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    public function index()
    {
        $courses = Course::all();
        foreach($courses as $course){
            if($this->user->hasCourse($course->id)){
                $course->registered_on = $this->user->getRegisteredDate($course->id)[0]->created_at;
            }
        }
        return response()->json(['courses'=>$courses]);
    }
    public function create()
    {
        $job = (new CreateCourseJob())->delay(Carbon::now()->addSeconds(3));
        dispatch($job);
        return response()->json(['success' => true, 'message'=>"Successfully created courses"], 200);
    }
    public function register(Request $request)
    {
        $courses = $request->only('courses');

        foreach($courses as $c){
            $this->user->courses()->attach($c, ['created_at'=>date('Y-m-d H:i:s'), 'updated_at'=> date('Y-m-d H:i:s')]);
        }
        return response()->json(['success'=>true, 'message'=>'Successfully registered for the course(s)']);
    }
    public function export($type)
    {
        switch ($type) {
            case 'excel':
                Excel::store(new CoursesExport, 'courses.xlsx', 'public');
                return response()->json(['success'=>true, 'path'=>asset('storage/courses.xlsx')]);
                break;
            case 'csv':
                Excel::store(new CoursesExport, 'courses.csv', 'public');
                return response()->json(['success'=>true, 'path'=>asset('storage/courses.csv')]);
                break;
            default:
                Excel::store(new CoursesExport, 'courses.xlsx', 'public');
                return response()->json(['success'=>true, 'path'=>asset('storage/courses.xlsx')]);
                break;
        }
    }
}
