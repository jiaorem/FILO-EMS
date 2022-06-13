<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Employee;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;
use DateTime;


class AttendanceController extends Controller
{
    public function punchIn (Request $request)
    {
        
        $dt         = Carbon::now();
        $todayDate  = $dt->toDayDateTimeString();
        $employee_id = Auth::User()->rec_id;
        $name = Auth::User()->name;
        
        $attendance = [
            'employee_id' => $employee_id,
            'name'        => $name,
            'punch_in'    => $todayDate,
        ]; 
            
        DB::table('employee_attendance')->insert($attendance);
        Toastr::success('Punched in successfully','Success');
        return redirect('home');
    }   

    public function punchOut (Request $request)
    {
        $user = Auth::User();
        Session::put('user', $user);
        $user=Session::get('user');

        $dt         = Carbon::now();
        $todayDate  = $dt->toDayDateTimeString();
        $employee_id = $user->rec_id;

        $punch_in = new DateTime($request->punch_in);
        $punch_out = $todayDate;
        $punch_out_hours = new DateTime ($punch_out);
        $hour     = $punch_in->diff($punch_out_hours);
        $office_hours    = $hour->h;
        
        $attendance = [
            'punch_out'    => $punch_out,
            'office_hour'    => $office_hours,
        ]; 

        DB::table('employee_attendance')->where('employee_id', $employee_id)->update($attendance);
        return redirect('home');
    }
}
