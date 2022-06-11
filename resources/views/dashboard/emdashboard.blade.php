@extends('layouts.master')
{{-- @section('menu')
@extends('sidebar.dashboard')
@endsection --}}
@section('content')

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title">
                        <span>Main</span>
                    </li>
                    <li>
                        <a href="{{ route('home') }}">
                            <i class="la la-dashboard" ></i>
                            <span>{{ Auth::user()->role_name }} Dashboard</span>
                        </a>
                    </li>
                    @if (Auth::user()->role_name=='Admin')
                        <li class="menu-title"> <span>Authentication</span> </li>
                        <li class="submenu">
                            <a href="#">
                                <i class="la la-user-secret"></i>
                                <span> User Controller</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul style="display: none;">
                                <li><a href="{{ route('userManagement') }}">All User</a></li>
                                <li><a href="{{ route('activity/log') }}">Activity Log</a></li>
                                <li><a href="{{ route('activity/login/logout') }}">Activity User</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="menu-title"> <span>Employees</span> </li>
                    <li class="submenu">
                        <a href="#">
                            <i class="la la-user"></i>
                            <span> Employees</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul style="display: none;">
                            @if (Auth::user()->role_name=='Admin')
                            <li><a href="{{ route('all/employee/list') }}">All Employees</a></li>
                            <li><a href="{{ route('attendance/page') }}">Attendance (Admin)</a></li>
                            <li><a href="{{ route('form/leaves/new') }}">Leaves (Admin)</a></li>
                            <li><a href="{{ route('form/holidays/new') }}">Holidays</a></li>
                            @else
                            <li><a href="{{ route('attendance/employee/page') }}">Attendance (Employee)</a></li>
                            <li><a href="{{route('form/leavesemployee/new')}}">Leaves (Employee)</a></li>
                            <li><a href="{{ route('form/holidays/new') }}">Holidays</a></li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Sidebar -->
    
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <!-- Page Content -->
        <div class="content container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="welcome-box">
                        <div class="welcome-img">
                            <img src="{{ URL::to('/assets/images/'. Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                        </div>
                        <div class="welcome-det">
                            <h2>Welcome {{ Auth::user()->role_name }}!</h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
            <div class="card-body">
                            <h2 class="card-title">Timesheet <small class="text-muted">{{\Carbon\Carbon::now()->format('d M, Y')}}</small></h2>
                            @if(!empty($time))
                            <div class="punch-det">
                                <h4>Punch In at</h4>
                                <p>{{$time->punch_in}}</p>
                                <br>
                                <h4>Punch Out at</h4>
                                <p>{{$time->punch_out}}</p>
                            </div>
                            <div class="punch-info">
                                <div class="punch-hours">
                                    <span>{{$time->office_hour}} hours</span>
                                </div>
                            </div>
                            @else
                            <div class="punch-det">
                                <h4>Punch In at</h4>
                                <p></p>
                                <br>
                                <h4>Punch Out at</h4>
                                <p></p>
                            </div>
                            <div class="punch-info">
                                <div class="punch-hours">
                                    <span>0 hours</span>
                                </div>
                            </div>
                            @endif

                            <form name="attendance" action="{{ route('home/punchIn') }}" method="POST">
                            @csrf
                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary submit-btn">Punch In</button>
                            </div>
                            </form>

                            <form name="attendance" action="{{ route('home/punchOut') }}" method="POST">
                            @csrf
                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary submit-btn">Punch Out</button>
                            </div>
                            </form>
                            </div>
                <div class="col-lg-4 col-md-4" align="center" >
                    <div class="dash-sidebar" >
                        <section>
                            <br><br><br><br>
                            <h5 class="dash-title">Apply For Leave</h5>
                                <div class="btn request-btn btn-primary" data-toggle="modal" data-target="#add_leave" >Apply Leave
                                </div>
                        </section>
                        <br><br><br>
                        <section>
                        <h5 class="dash-title">Upcoming Holidays</h5>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table">
                                    <table class="table table-striped custom-table">
                                        <thead>
                                            <tr>
                                                <th>Holiday</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if(!empty($holidays))
                                            @foreach ($holidays as $items ) 
                                                <tr>
                                                    <td class="id">{{$items->name_holiday}}</td>
                                                    <td class="punch_in">{{$items->date_holiday}}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        </section>
                    </div>
                </div>
            </div>

        </div>
<div class="row">
        <div class="col-md-12">
                    <div class="table-responsive">
                    <table class="table table-striped custom-table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Punch In</th>
                                <th>Punch Out</th>
                                <th>Office Hour</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(!empty($attendance))
                            @foreach ($attendance as $items) 
                                <tr>
                                    <td class="id">{{$items->id}}</td>
                                    <td class="punch_in">{{$items->punch_in}}</td>
                                    <td class="punch_out">{{$items->punch_out}}</td>
                                    <td class="office_hour">{{$items->office_hour}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        <!-- /Page Content -->
    </div>
    <!-- Add Leave Modal -->
    <div id="add_leave" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Leave</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('form/leaves/save') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Leave Type <span class="text-danger">*</span></label>
                                <select class="select" id="leaveType" name="leave_type">
                                    <option selected disabled>Select Leave Type</option>
                                    <option value="Casual Leave 12 Days">Casual Leave 12 Days</option>
                                    <option value="Medical Leave">Medical Leave</option>
                                    <option value="Loss of Pay">Loss of Pay</option>
                                </select>
                            </div>
                            <input type="hidden" class="form-control" id="rec_id" name="rec_id" value="{{ Auth::user()->rec_id }}">
                            <div class="form-group">
                                <label>From <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control datetimepicker" id="from_date" name="from_date">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>To <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control datetimepicker" id="to_date" name="to_date">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Leave Reason <span class="text-danger">*</span></label>
                                <textarea rows="4" class="form-control" id="leave_reason" name="leave_reason"></textarea>
                            </div>
                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Leave Modal -->
    <!-- /Page Wrapper -->  
@endsection
