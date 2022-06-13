<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use DB;
use App\Models\User;
use App\Models\Employee;
use App\Models\Form;
use App\Models\ProfileInformation;
use App\Rules\MatchOldPassword;
use Carbon\Carbon;
use Session;
use Auth;
use Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        if (Auth::user()->role_name=='Admin')
        {
            $result      = DB::table('users')->get();
            $role_name   = DB::table('role_type_users')->get();
            return view('usermanagement.user_control',compact('result','role_name'));
        }
        else
        {
            return redirect()->route('home');
        }
        
    }

    // use activity log
    public function activityLog()
    {
        $activityLog = DB::table('user_activity_logs')->get();
        return view('usermanagement.user_activity_log',compact('activityLog'));
    }
    // activity log
    public function activityLogInLogOut()
    {
        $activityLog = DB::table('activity_logs')->get();
        return view('usermanagement.activity_log',compact('activityLog'));
    }

    // profile user
    public function profile()
    {   
        $user = Auth::User();
        Session::put('user', $user);
        $user=Session::get('user');
        $profile = $user->rec_id;
        $email = $user->email;
        $user = DB::table('users')->get();
        $information = DB::table('profile_information')->where('email',$email)->first();

        return view('usermanagement.profile_user',compact('information','user'));
    }

    // save profile information
    public function profileInformation(Request $request)
    {
        try{
            if(!empty($request->images))
            {
                $image_name = $request->hidden_image;
                $image = $request->file('images');
                if($image_name =='photo_defaults.jpg')
                {
                    if($image != '')
                    {
                        $image_name = rand() . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('/assets/images/'), $image_name);
                    }
                }
                else{
                    if($image != '')
                    {
                        $image_name = rand() . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('/assets/images/'), $image_name);
                    }
                }
            } 
            
            $information = ProfileInformation::updateOrCreate(['email' => $request->email]);
            $information->name         = $request->name;
            $information->rec_id       = $request->rec_id;
            $information->email        = $request->email;
            $information->birth_date   = $request->birthDate;
            $information->gender       = $request->gender;
            $information->address      = $request->address;
            $information->state        = $request->state;
            $information->country      = $request->country;
            $information->pin_code     = $request->pin_code;
            $information->phone_number = $request->phone_number;
            $information->save();

            $update = User::updateOrCreate(['email' => $request->email]);
            $update->name         = $request->name;
            $update->email        = $request->email;
            $update->phone_number = $request->phone_number;
            $update->save();

            $employee = [
                'name'         => $request->name,
                'email'        => $request->email,
                'birth_date'   => $request->birthDate,
                'gender'       => $request->gender,
            ];
            
            $employees_table = DB::table('employees')->where('email',$request->email)->first();
            if (!empty($employees_table)){
                DB::table('employees')->where('email',$request->email)->update($employee);
            }

            DB::commit();
            Toastr::success('Profile Information successfully','Success');
            return redirect()->back();
        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('Add Profile Information fail','Error');
            return redirect()->back();
        }
    }
   
    // save new user
    public function addNewUserSave(Request $request)
    {

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'phone'     => 'required|min:11|numeric',
            'role_name' => 'required|string|max:255',
            'image'     => 'required|image',
            'password'  => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);
        DB::beginTransaction();
        try{
            $dt       = Carbon::now();
            $todayDate = $dt->toDayDateTimeString();

            $image = time().'.'.$request->image->extension();  
            $request->image->move(public_path('assets/images'), $image);

            $user = new User;
            $user->name         = $request->name;
            $user->email        = $request->email;
            $user->join_date    = $todayDate;
            $user->phone_number = $request->phone;
            $user->role_name    = $request->role_name;
            $user->avatar       = $image;
            $user->password     = Hash::make($request->password);
            $user->save();

            DB::commit();

            $gender = "";
            $profile = [
                'name'              =>$request->name,
                'birth_date'        => $request->birthDate,
                'phone_number'      => $request->phone,
                'email'             =>$request->email,
                'gender'            =>$gender
            ];

            DB::table('profile_information')->insert($profile);
            Toastr::success('Create new account successfully','Success');
            return redirect()->route('userManagement');
        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('User add new account fail','Error');
            return redirect()->back();
        }
    }
    
    // update
    public function update(Request $request)
    {
        DB::beginTransaction();
        try{
            $rec_id       = $request->rec_id;
            $name         = $request->name;
            $email        = $request->email;
            $role_name    = $request->role_name;
            $phone        = $request->phone;

            $dt       = Carbon::now();
            $todayDate = $dt->toDayDateTimeString();
            $image_name = $request->hidden_image;
            $image = $request->file('images');
            if($image_name =='photo_defaults.jpg')
            {
                if($image != '')
                {
                    $image_name = rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('/assets/images/'), $image_name);
                }
            }
            else{
                
                if($image != '')
                {
                    $image_name = rand() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('/assets/images/'), $image_name);
                }
            }
            
            $update = [

                'rec_id'       => $rec_id,
                'name'         => $name,
                'role_name'    => $role_name,
                'email'        => $email,
                'phone_number' => $phone,
                'avatar'       => $image_name,
            ];

            $activityLog = [
                'user_name'    => $name,
                'email'        => $email,
                'phone_number' => $phone,
                'role_name'    => $role_name,
                'modify_user'  => 'Update',
                'date_time'    => $todayDate,
            ];

            $profile = [
                'name'         => $name,
                'rec_id'       => $rec_id,
                'email'        => $email,
                'phone_number' => $phone,
            ];

            $employee = [
                'name'         => $name,
                'email'        => $email,
            ];
            
            ProfileInformation::where('email',$request->email)->update($profile);
            DB::table('user_activity_logs')->insert($activityLog);
            User::where('rec_id',$request->rec_id)->update($update);
            Employee::where('employee_id',$request->rec_id)->update($employee);
            
            DB::commit();
            Toastr::success('User updated successfully','Success');
            return redirect()->route('userManagement');

        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('User update fail','Error');
            return redirect()->back();
        }
    }
    // delete
    public function delete(Request $request)
    {
        DB::beginTransaction();
        try{
            $fullName     = $request->name;
            $email        = $request->email;
            $phone_number = $request->phone_number;
            $role_name    = $request->role_name;
            $rec_id       = $request->rec_id;

            $dt       = Carbon::now();
            $todayDate = $dt->toDayDateTimeString();

            $activityLog = [

                'user_name'    => $fullName,
                'email'        => $email,
                'phone_number' => $phone_number,
                'role_name'    => $role_name,
                'modify_user'  => 'Delete',
                'date_time'    => $todayDate,
            ];

            DB::table('user_activity_logs')->insert($activityLog);
            User::destroy($request->rec_id);
            DB::table('profile_information')->where('email',$request->email)->delete();

            
            if($request->avatar =='photo_defaults.jpg'){
                User::destroy($request->id);
            }else{
                User::destroy($request->id);
                unlink('assets/images/'.$request->avatar);
            }
            DB::commit();
            Toastr::success('User deleted successfully','Success');
            return redirect()->route('userManagement');
            
        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('User deleted fail','Error');
            return redirect()->back();
        }
    }

    // view change password
    public function changePasswordView()
    {
        return view('settings.changepassword');
    }
    
    // change password in db
    public function changePasswordDB(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
        DB::commit();
        Toastr::success('User change successfully','Success');
        return redirect()->intended('home');
    }
}









