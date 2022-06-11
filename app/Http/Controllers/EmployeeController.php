<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Employee;
use App\Models\User;
use App\Models\ProfileInformation;

class EmployeeController extends Controller
{
    // all employee card view
    public function cardAllEmployee(Request $request)
    {
        $users = DB::table('users')
                    ->join('employees', 'users.rec_id', '=', 'employees.employee_id')
                    ->select('users.*', 'employees.birth_date', 'employees.gender')
                    ->get(); 
        $userList = DB::table('users')->get();
        return view('form.allemployeecard',compact('users','userList'));
    }
    // all employee list
    public function listAllEmployee()
    {
        $users = DB::table('users')
                    ->join('employees', 'users.rec_id', '=', 'employees.employee_id')
                    ->select('users.*', 'employees.birth_date', 'employees.gender')
                    ->get();
        $userList = DB::table('users')
                    ->where('role_name','!=','Admin')
                    ->get();
        return view('form.employeelist',compact('users','userList'));
    }

    // save data employee
    public function saveRecord(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email',
            'birthDate'   => 'required|string|max:255',
            'gender'      => 'required|string|max:255',
            'employee_id' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try{

            $employees = Employee::where('email', '=',$request->email)->first();
            if ($employees === null)
            {

                $employee = new Employee;
                $employee->name         = $request->name;
                $employee->email        = $request->email;
                $employee->birth_date   = $request->birthDate;
                $employee->gender       = $request->gender;
                $employee->employee_id  = $request->employee_id;
                $employee->save();
                
                $profile = [
                    'name'              =>$request->name,
                    'birth_date'        => $request->birthDate,
                    'gender'            => $request->gender,
                    'email'             =>$request->email,
                ];
                
                DB::table('profile_information')->update($profile);
                DB::commit();
                Toastr::success('Add new employee successfully :)','Success');
                return redirect()->route('all/employee/list');
            } else {
                DB::rollback();
                Toastr::error('Add new employee exits','Error');
                return redirect()->back();
            }
        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('Add new employee fail :)','Error');
            return redirect()->back();
        }
    }
    // view edit record
    public function viewRecord($employee_id)
    {
        $employees = DB::table('employees')->where('employee_id',$employee_id)->get();
        return view('form.edit.editemployee',compact('employees'));
    }
    // update record employee
    public function updateRecord( Request $request)
    {
        DB::beginTransaction();
        try{
            // update table Employee
            $updateEmployee = [
                'id'=>$request->id,
                'name'=>$request->name,
                'email'=>$request->email,
                'birth_date'=>$request->birth_date,
                'gender'=>$request->gender,
                'employee_id'=>$request->employee_id,
            ];
            // update table user
            $updateUser = [
                'id'=>$request->id,
                'name'=>$request->name,
                'email'=>$request->email,
            ];

            // update table profile_information
            $profile = [
                'name'              =>$request->name,
                'birth_date'        => $request->birth_date,
                'gender'            => $request->gender,
                'email'             =>$request->email,
            ];
            
            ProfileInformation::where('rec_id',$request->employee_id)->update($profile);
            User::where('rec_id',$request->employee_id)->update($updateUser);
            Employee::where('id',$request->id)->update($updateEmployee);
        
            DB::commit();
            Toastr::success('updated record successfully :)','Success');
            return redirect()->route('all/employee/list');
        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('updated record fail :)','Error');
            return redirect()->back();
        }
    }
    // delete record
    public function deleteRecord($employee_id)
    {
        DB::beginTransaction();
        try{

            Employee::where('employee_id',$employee_id)->delete();

            DB::commit();
            Toastr::success('Delete record successfully :)','Success');
            return redirect()->route('all/employee/list');

        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('Delete record fail :)','Error');
            return redirect()->back();
        }
    }
}
