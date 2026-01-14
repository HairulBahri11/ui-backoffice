@if (Auth::guard('teacher')->user() != null || Auth::guard('staff')->user() != null)
<div class="sidebar sidebar-style-2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-left mr-2">
                    <img src="{{ asset('assets/img/profile.png') }}" alt="..." class="avatar-img rounded-circle">
                </div>
                <div class="info">
                    <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">

                        <span class="user-level">

                            {{ (Auth::guard('teacher')->user() != null ? Auth::guard('teacher')->user()->name : Auth::guard('staff')->user()->name) ?? '' }}
                        </span>
                    </a>

                </div>
            </div>
            <ul class="nav nav-primary">
                @if (Auth::guard('teacher')->user() != null)
                <li class="nav-item {{ Request::segment(1) == 'dashboard' ? 'active' : '' }}">
                    <a href="{{ url('/dashboard') }}" class="collapsed">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>

                </li>
                @endif
                <li
                    class="nav-section {{ Request::segment(1) == 'admin' || Request::segment(1) == 'petani' ? 'active' : '' }}">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Menu</h4>
                </li>


                <li class="nav-item {{ Request::segment(1) == 'attendances' ? 'active' : '' }}">
                    <a href="{{ url('/attendance/class') }}" class="collapsed">
                        <i class="fas fa-book"></i>
                        <p>Attendance</p>
                    </a>

                </li>
                <li class="nav-item {{ Request::segment(1) == 'score' ? 'active' : '' }}">
                    <a href="{{ url('/score/form') }}" class="collapsed">
                        <i class="fas fa-user-alt"></i>
                        <p>Student's Score</p>
                    </a>

                </li>

                @if (Auth::guard('teacher')->user() == null)
                <li
                    class="nav-item {{ Request::segment(1) == 'advertise' || Request::segment(1) == 'announces' || Request::segment(1) == 'pointCategories' || Request::segment(1) == 'reedemItems' || Request::segment(1) == 'testMaster' || Request::segment(1) == 'tests' || Request::segment(1) == 'parents' ? 'active' : '' }} submenu">
                    <a data-toggle="collapse" href="#tables">
                        <i class="fas fa-th-large"></i>
                        <p>Master Data</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ Request::segment(1) == 'advertise' || Request::segment(1) == 'announces' || Request::segment(1) == 'pointCategories' || Request::segment(1) == 'reedemItems' || Request::segment(1) == 'testMaster' || Request::segment(1) == 'tests' || Request::segment(1) == 'parents' ? 'show' : '' }}"
                        id="tables">
                        <ul class="nav nav-collapse">
                            <li class="{{ Request::segment(1) == 'parents' ? 'active' : '' }}">
                                <a href="{{ url('/parents') }}">
                                    <span class="sub-item ">Parents</span>
                                </a>
                            </li>
                            <li class="{{ Request::segment(1) == 'tests' ? 'active' : '' }}">
                                <a href="{{ url('/tests') }}">
                                    <span class="sub-item ">Test</span>
                                </a>
                            </li>
                            <li class="{{ Request::segment(1) == 'pointCategories' ? 'active' : '' }}">
                                <a href="{{ url('/pointCategories') }}">
                                    <span class="sub-item ">Point Categories</span>
                                </a>
                            </li>
                            <li class="{{ Request::segment(1) == 'reedemItems' ? 'active' : '' }}">
                                <a href="{{ url('/reedemItems') }}">
                                    <span class="sub-item ">Reedem Item</span>
                                </a>
                            </li>
                            <li class="{{ Request::segment(1) == 'announces' ? 'active' : '' }}">
                                <a href="{{ url('/announces') }}">
                                    <span class="sub-item ">Announcement</span>
                                </a>
                            </li>
                            <li class="{{ Request::segment(1) == 'advertise' ? 'active' : '' }}">
                                <a href="{{ url('/advertise') }}">
                                    <span class="sub-item ">Advertise</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item {{ Request::segment(1) == 'reedemPoint' ? 'active' : '' }}">
                    <a href="{{ url('/reedemPoint') }}" class="collapsed">
                        <i class="fas fa-download"></i>
                        <p>Reedem Point</p>
                    </a>

                </li>
                <li class="nav-item {{ Request::segment(1) == 'birthday-point' ? 'active' : '' }}">
                    <a href="{{ url('/birthday-point') }}" class="collapsed">
                        <i class="fas fa-birthday-cake"></i>
                        <p>Student's Birthday</p>
                    </a>

                </li>
                @if (Auth::guard('staff')->check() == true)
                <li class="nav-item {{ Request::segment(1) == 'extra-point' ? 'active' : '' }}">
                    <a href="{{ url('/extra-point') }}" class="collapsed">
                        <i class="fas fa-star"></i>
                        <p>Extra Point</p>
                    </a>
                </li>
                @endif
                <li class="nav-item {{ Request::segment(1) == 'schedule-class' ? 'active' : '' }}">
                    <a href="{{ url('/schedule-class') }}" class="collapsed">
                        <i class="fas fa-calendar"></i>
                        <p>Class Schedule</p>
                    </a>

                </li>
                <li class="nav-item {{ Request::segment(1) == 'mutasi' ? 'active' : '' }}">
                    <a href="{{ url('/mutasi') }}" class="collapsed">
                        <i class="fas fa-users"></i>
                        <p>Jump Level</p>
                    </a>

                </li>

                @if (Auth::guard('staff')->check() == true)
                 <li class="nav-item {{ Request::segment(1) == 'class-info' ? 'active' : '' }}">
                    <a href="{{ url('/class-info') }}" class="collapsed">
                        <i class="fas fa-info"></i>
                        <p>Class Monitoring</p>
                    </a>

                </li>
                @endif
                @if (Auth::guard('teacher')->user() == null)
                <li class="nav-item {{ Request::segment(1) == 'history-test' ? 'active' : '' }}">
                    <a href="{{ url('/history-test') }}" class="collapsed">
                        <i class="fas fa-book"></i>
                        <p>Test History</p>
                    </a>

                </li>
                @endif
                @endif

                <li class="nav-item {{ Request::segment(2) == 'reminder' ? 'active' : '' }}">
                    <a href="{{ url('/attendance/reminder') }}" class="collapsed">
                        <i class="fas fa-bell"></i>
                        <p>Absence Reminder</p>
                    </a>

                </li>

               
                 <li class="nav-item {{ Request::segment(1) == 'calendar' ? 'active' : '' }}">
                    <a href="{{ url('/calendar') }}" class="collapsed">
                        <i class="fas fa-calendar"></i>
                        <p>Weekly Schedule</p>
                    </a>

                </li>
               


                @if(Auth::guard('teacher')->check() == true && Auth::guard('teacher')->user()->id == 20 || Auth::guard('teacher')->check() == true &&Auth::guard('teacher')->user()->id == 21)

                <li class="nav-item {{ Request::segment(1) == 'teacher-reminder' ? 'active' : '' }}">
                    <a href="{{ url('/teacher-reminder') }}" class="collapsed">
                        <i class="fas fa-user-clock"></i>
                        <p>Teacher's Reminders</p>
                    </a>
                </li>

                @endif
                @if (Auth::guard('staff')->check() == true)
                <!-- teacher notes -->
                <li class="nav-item {{ Request::segment(1) == 'teacher-notes' ? 'active' : '' }}">
                    <a href="{{ url('/teacher-notes') }}" class="collapsed">
                        <i class="fas fa-address-book"></i>
                        <p>Teacher's Notes</p>
                    </a>
                </li>
                @endif
                @if (Auth::guard('staff')->check() == true)
                <li class="nav-item {{ Request::segment(1) == 'saldo-awal' ? 'active' : '' }}">
                    <a href="{{ url('/saldo-awal') }}" class="collapsed">
                        <i class="fas fa-upload"></i>
                        <p>Opening Balance</p>
                    </a>

                </li>
                @elseif (Auth::guard('teacher')->user()->id == 20 || Auth::guard('teacher')->user()->id == 21)
                <li class="nav-item {{ Request::segment(1) == 'saldo-awal' ? 'active' : '' }}">
                    <a href="{{ url('/saldo-awal') }}" class="collapsed">
                        <i class="fas fa-upload"></i>
                        <p>Opening Balance</p>
                    </a>

                </li>
                @endif
                <li class="nav-item {{ Request::segment(1) == 'history-point' ? 'active' : '' }}">
                    <a href="{{ url('/history-point') }}" class="collapsed">
                        <i class="fas fa-book"></i>
                        <p>Point History</p>
                    </a>

                </li>
                <li class="nav-item {{ Request::segment(1) == 'review' ? 'active' : '' }}">
                    <a href="{{ url('/review') }}" class="collapsed">
                        <i class="fas fa-book"></i>
                        <p>Review &Test Order</p>
                    </a>

                </li>
                <li class="nav-item {{ Request::segment(1) == 'result-form' ? 'active' : '' }}">
                    <a href="{{ url('/result-form') }}" class="collapsed">
                        <i class="fas fa-pencil-alt"></i>
                        <p>Poor Result</p>

                        {{-- @if (isset($count_result) && $count_result > 0)
                                <span class="badge badge-danger">{{ $count_result }}</span>
                        @endif --}}

                    </a>

                </li>
                @if (Auth::guard('teacher')->user() == null)
                <li class="nav-item {{ Request::segment(1) == 'barcode-student' ? 'active' : '' }}">
                    <a href="{{ url('/barcode-student') }}" class="collapsed">
                        <i class="fas fa-barcode"></i>
                        <p>Student’s Barcode</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::segment(1) == 'e-certificate' ? 'active' : '' }}">
                    <a href="{{ url('/e-certificate') }}" class="collapsed">
                        <i class="fas fa-certificate"></i>
                        <p>Certificate Authorization</p>
                    </a>

                </li>
                <li class="nav-item {{ Request::segment(1) == 'history-certificate' ? 'active' : '' }}">
                    <a href="{{ url('/history-certificate') }}" class="collapsed">
                        <i class="fas fa-certificate"></i>
                        <p>History Certificate</p>
                    </a>

                </li>
                {{-- history chat --}}
                <li class="nav-item {{ Request::segment(1) == 'history-chat' ? 'active' : '' }}">
                    <a href="{{ url('/history-chat') }}" class="collapsed">
                        <i class="fas fa-comments"></i>
                        <p>History Chat</p>
                    </a>
                </li>
                @endif
                <li class="nav-item {{ Request::segment(1) == 'follow-up' ? 'active' : '' }}">
                    <a href="{{ url('/follow-up') }}" class="collapsed">
                        <i class="fas fa-database"></i>
                        <p>Follow Up</p>
                    </a>

                </li>
            </ul>
        </div>
    </div>
</div>
@else
<script>
    window.location = "{{ url('/') }}";
</script>
@endif