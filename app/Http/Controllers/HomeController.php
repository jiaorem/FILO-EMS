<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use PDF;
use App\Models\User;
use Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // main dashboard
    public function index()
    {
        if (Auth::id()) {
            if (Auth::user()->role_name=='Admin') {
                $users = DB::table('users')
                    ->join('employees', 'users.rec_id', '=', 'employees.employee_id')
                    ->select('users.*', 'employees.birth_date', 'employees.gender')
                    ->get(); 
                $userList = DB::table('users')->get();
                $employee = DB::table('employees')->first();
                return view('dashboard.dashboard',compact('users','userList','employee'));
                //return view('dashboard.dashboard');
            }
            else{
                $time = DB::table('employee_attendance')
                ->orderByDesc('id')
                ->first();
                $employee_id = Auth::user()->rec_id;
                $attendance = DB::table('employee_attendance')->where('employee_id', $employee_id)->get();
                $holidays = DB::table('holidays')->get();
                return view('dashboard.emdashboard', compact('time','holidays','attendance'));
            }
            //Auth::logout();
        }
        else{
            return redirect()->back();
        }
    }
}
