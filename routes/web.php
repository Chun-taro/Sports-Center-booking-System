<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CourtController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\FacilityController as CustomerFacilityController;
use App\Http\Controllers\Customer\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/calendar-events', [HomeController::class, 'calendarEvents'])->name('calendar.public-events');
Route::get('/facilities', [CustomerFacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [CustomerFacilityController::class, 'show'])->name('facilities.show');
Route::get('/facilities/{facility}/calendar-events', [CustomerFacilityController::class, 'calendarEvents'])->name('facilities.calendar-events');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Customer Only Routes (Strictly Role: Customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/booking/wizard', [CustomerBookingController::class, 'wizard'])->name('customer.bookings.wizard');
    Route::post('/booking/check-availability', [CustomerBookingController::class, 'checkAvailability'])->name('customer.bookings.check-availability');
    Route::get('/booking/get-courts', [CustomerBookingController::class, 'getCourts'])->name('customer.bookings.get-courts');
    Route::post('/booking/store', [CustomerBookingController::class, 'store'])->name('customer.bookings.store');
    
    Route::get('/my-bookings', [CustomerBookingController::class, 'index'])->name('customer.bookings.index');
    Route::get('/my-bookings/{booking}', [CustomerBookingController::class, 'show'])->name('customer.bookings.show');
    Route::post('/my-bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('customer.bookings.cancel');

    Route::get('/profile', [AuthController::class, 'profile'])->name('customer.profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('customer.profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin & Staff Shared Management Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Facilities (View list allowed for Staff & Admin)
    Route::get('/facilities', [AdminFacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/{facility}', [AdminFacilityController::class, 'show'])->name('facilities.show');

    // Courts (View list allowed for Staff & Admin)
    Route::get('/courts', [CourtController::class, 'index'])->name('courts.index');

    // Bookings Management (Staff & Admin)
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{booking}/check-in', [AdminBookingController::class, 'checkIn'])->name('bookings.check-in');
    Route::post('/bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
    Route::put('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');

    // Schedules & Operating Hours (Staff & Admin)
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::put('/schedules/operating-hours/{facility}', [ScheduleController::class, 'updateOperatingHours'])->name('schedules.operating-hours');
    Route::post('/schedules/holidays', [ScheduleController::class, 'storeHoliday'])->name('schedules.holidays.store');
    Route::delete('/schedules/holidays/{holiday}', [ScheduleController::class, 'destroyHoliday'])->name('schedules.holidays.destroy');

    // Payments Ledger (Staff & Admin)
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::put('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');

    // Reports (Staff & Admin)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');

    /*
    |--------------------------------------------------------------------------
    | Administrator ONLY Routes (Strictly Role: Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Facility Mutation (Add, Edit, Delete)
        Route::get('/facilities/create', [AdminFacilityController::class, 'create'])->name('facilities.create');
        Route::post('/facilities', [AdminFacilityController::class, 'store'])->name('facilities.store');
        Route::get('/facilities/{facility}/edit', [AdminFacilityController::class, 'edit'])->name('facilities.edit');
        Route::put('/facilities/{facility}', [AdminFacilityController::class, 'update'])->name('facilities.update');
        Route::delete('/facilities/{facility}', [AdminFacilityController::class, 'destroy'])->name('facilities.destroy');

        // Court Mutation (Add, Edit, Delete)
        Route::post('/courts', [CourtController::class, 'store'])->name('courts.store');
        Route::put('/courts/{court}', [CourtController::class, 'update'])->name('courts.update');
        Route::delete('/courts/{court}', [CourtController::class, 'destroy'])->name('courts.destroy');
    });
});
