<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'School Management System')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            color: white;
            overflow-y: auto;
            z-index: 999;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-brand h4 {
            margin: 0;
            font-weight: bold;
            font-size: 18px;
        }

        .sidebar-brand small {
            display: block;
            opacity: 0.8;
            margin-top: 5px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--secondary-color);
            padding-left: 25px;
        }

        .sidebar-menu i {
            width: 20px;
            margin-right: 10px;
        }

        /* Top Navigation */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            height: 60px;
            background: white;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            z-index: 998;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .top-navbar .navbar-brand {
            font-weight: bold;
            color: var(--primary-color);
            margin: 0;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .navbar-right .dropdown-toggle {
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 500;
            cursor: pointer;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            margin-top: 60px;
            padding: 30px;
            min-height: calc(100vh - 60px);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            color: white;
            border-radius: 8px 8px 0 0;
            padding: 15px 20px;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Tables */
        .table {
            background: white;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: var(--primary-color);
            font-weight: 600;
            border: 1px solid #dee2e6;
        }

        .table tbody td {
            vertical-align: middle;
            border: 1px solid #dee2e6;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 1px solid #d0d0d0;
            border-radius: 5px;
            padding: 10px 15px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        /* Alerts */
        .alert {
            border-radius: 5px;
            border: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                z-index: 1000;
            }

            .top-navbar,
            .main-content {
                margin-left: 0;
                margin-top: 0;
            }

            .sidebar-menu a {
                display: inline-block;
                width: auto;
                padding: 10px 15px;
            }

            .top-navbar {
                position: relative;
                left: 0;
                top: 0;
            }

            .main-content {
                margin-top: 0;
                padding: 15px;
            }
        }

        /* Login Page Styles */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        .login-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }

        .login-box h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
            text-align: center;
            font-weight: bold;
        }

        .login-box .form-control {
            margin-bottom: 15px;
            padding: 12px 15px;
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            font-weight: 500;
        }
    </style>

    @stack('styles')
</head>
<body>
    @if(session('user_type'))
        <!-- Sidebar (only when logged in) -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <h4><i class="fas fa-graduation-cap"></i> SMS</h4>
                <small>SMPIT HANIA</small>
            </div>
            
            @if(session('user_type') === 'employee')
                <ul class="sidebar-menu">
                    <li><a href="{{ route('employee.dashboard') }}" class="@if(Route::currentRouteName() === 'employee.dashboard') active @endif"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                   <li>
    <a href="{{ route('employee.employees.index') }}"
       class="{{ request()->routeIs('employee.employees.*') ? 'active' : '' }}">
        <i class="fas fa-users"></i> Employees
    </a>
</li>

<li>
    <a href="{{ route('employee.teachers.index') }}"
       class="{{ request()->routeIs('employee.teachers.*') ? 'active' : '' }}">
        <i class="fas fa-chalkboard-user"></i> Teachers
    </a>
</li>

<li>
    <a href="{{ route('employee.students.index') }}"
       class="{{ request()->routeIs('employee.students.*') ? 'active' : '' }}">
        <i class="fas fa-book"></i> Students
    </a>
</li>
<li>
    <a href="{{ route('employee.events.index') }}"
       class="{{ request()->routeIs('employee.events.*') ? 'active' : '' }}">
        <i class="fas fa-calendar"></i> Events
    </a>
</li>

<li>
    <a href="{{ route('employee.academic-years.index') }}"
       class="{{ request()->routeIs('employee.academic-years.*') ? 'active' : '' }}">
        <i class="fas fa-calendar-alt"></i> Academic Years
    </a>
</li>
<li>
    <a href="{{ route('employee.classes.index') }}"
       class="{{ request()->routeIs('employee.classes.*') ? 'active' : '' }}">
        <i class="fas fa-door-open"></i> Classes
    </a>
</li>
<li>
    <a href="{{ route('employee.articles.index') }}"
       class="{{ request()->routeIs('employee.articles.*') ? 'active' : '' }}">
        <i class="fas fa-newspaper"></i> Articles
    </a>
</li>
<li>
    <a href="{{ route('employee.subjects.index') }}"
       class="{{ request()->routeIs('employee.subjects.*') ? 'active' : '' }}">
        <i class="fas fa-book-open"></i> Subjects
    </a>
</li>
<li>
    <a href="{{ route('employee.student-classes.index') }}"
       class="{{ request()->routeIs('employee.student-classes.*') ? 'active' : '' }}">
        <i class="fas fa-book-open"></i> student classes
    </a>
</li>
<li>
    <a href="{{ route('employee.attendance.index') }}"
       class="{{ request()->routeIs('employee.attendance.*') ? 'active' : '' }}">
        <i class="fas fa-book-open"></i> Transaksi attendance
    </a>
</li>
<li>
    <a href="{{ route('employee.schedules.index') }}"
       class="{{ request()->routeIs('employee.schedules.*') ? 'active' : '' }}">
        <i class="fas fa-book-open"></i> Transaksi Schedules
    </a>
</li>
<li>
    <a href="{{ route('employee.tag-articles.index') }}"
       class="{{ request()->routeIs('employee.tag-articles.*') ? 'active' : '' }}">
        <i class="fas fa-tags"></i> Tag Articles
    </a>
</li>
<li>
    <a href="{{ route('employee.tag-events.index') }}"
       class="{{ request()->routeIs('employee.tag-events.*') ? 'active' : '' }}">
        <i class="fas fa-tags"></i> Tag Events
    </a>
</li>
<li>
   <a href="{{ route('employee.settings.index-header') }}"
        class="{{ request()->routeIs('employee.settings.*') ? 'active' : '' }}">
        <i class="fas fa-cog"></i> Settings
    </a>
</li>

<li>
    <a href="{{ route('employee.grades.index') }}"
       class="{{ request()->routeIs('employee.grades.*') ? 'active' : '' }}">
        <i class="fas fa-star"></i> Grades
    </a>
</li>


                    <li><a href="{{ route('employee.logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            @elseif(session('user_type') === 'teacher')
                <ul class="sidebar-menu">
                    <li><a href="{{ route('teacher.dashboard') }}" class="@if(Route::currentRouteName() === 'teacher.dashboard') active @endif"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="{{ route('teacher.logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            @elseif(session('user_type') === 'student')
                <ul class="sidebar-menu">
                    <li><a href="{{ route('student.dashboard') }}" class="@if(Route::currentRouteName() === 'student.dashboard') active @endif"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="{{ route('student.profile') }}" class="@if(Route::currentRouteName() === 'student.profile') active @endif"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="{{ route('student.logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            @endif
        </div>

        <!-- Top Navigation -->
        <nav class="top-navbar">
            <h5 class="navbar-brand">@yield('page-title', 'Dashboard')</h5>
            <div class="navbar-right">
                <span>Welcome, <strong>{{ session('name') }}</strong></span>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            @yield('content')
        </div>
    @else
        <!-- Login Pages -->
        @yield('content')
    @endif

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
