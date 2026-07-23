<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Facility;
use App\Models\Holiday;
use App\Models\OperatingHour;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        $admin = User::create([
            'name' => 'Administrator Apex',
            'email' => 'admin@apexsports.com',
            'phone' => '+1 (555) 019-2831',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $staff = User::create([
            'name' => 'Alex Staffer',
            'email' => 'staff@apexsports.com',
            'phone' => '+1 (555) 014-9988',
            'role' => 'staff',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $customer1 = User::create([
            'name' => 'Michael Corleone',
            'email' => 'customer@apexsports.com',
            'phone' => '+1 (555) 012-3456',
            'role' => 'customer',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $customer2 = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'phone' => '+1 (555) 017-7744',
            'role' => 'customer',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $customer3 = User::create([
            'name' => 'David Vance',
            'email' => 'david@example.com',
            'phone' => '+1 (555) 018-8833',
            'role' => 'customer',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // 2. Create Facilities & Courts
        $facilitiesData = [
            [
                'name' => 'Apex Badminton Pavilion',
                'slug' => 'apex-badminton-pavilion',
                'sport_type' => 'badminton',
                'description' => 'Professional BWF-grade wooden flooring courts with glare-free LED illumination and air conditioning.',
                'image_url' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 25.00,
                'open_time' => '07:00:00',
                'close_time' => '23:00:00',
                'max_players' => 4,
                'courts' => [
                    ['name' => 'Court A (Premium Mat)', 'capacity' => 4, 'hourly_rate_override' => 28.00],
                    ['name' => 'Court B (Standard)', 'capacity' => 4, 'hourly_rate_override' => null],
                    ['name' => 'Court C (Standard)', 'capacity' => 4, 'hourly_rate_override' => null],
                    ['name' => 'Court D (Standard)', 'capacity' => 4, 'hourly_rate_override' => null],
                ]
            ],
            [
                'name' => 'Grand Slam Basketball Arena',
                'slug' => 'grand-slam-basketball-arena',
                'sport_type' => 'basketball',
                'description' => 'Full-size hardwood maple court equipped with FIBA breakaway rims and electronic scoreboard.',
                'image_url' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 60.00,
                'open_time' => '08:00:00',
                'close_time' => '22:00:00',
                'max_players' => 10,
                'courts' => [
                    ['name' => 'Main Championship Court', 'capacity' => 10, 'hourly_rate_override' => 75.00],
                    ['name' => 'Practice Half Court A', 'capacity' => 6, 'hourly_rate_override' => 40.00],
                ]
            ],
            [
                'name' => 'Pickleball Social Hub',
                'slug' => 'pickleball-social-hub',
                'sport_type' => 'pickleball',
                'description' => 'High-traction acrylic surface courts optimized for fast-paced pickleball rallies and social leagues.',
                'image_url' => 'https://images.unsplash.com/photo-1599586120429-48281b6f0eca?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 22.00,
                'open_time' => '08:00:00',
                'close_time' => '22:00:00',
                'max_players' => 4,
                'courts' => [
                    ['name' => 'Court 1 (Sun Shield Covered)', 'capacity' => 4, 'hourly_rate_override' => null],
                    ['name' => 'Court 2 (Sun Shield Covered)', 'capacity' => 4, 'hourly_rate_override' => null],
                    ['name' => 'Court 3 (Open Air)', 'capacity' => 4, 'hourly_rate_override' => 20.00],
                ]
            ],
            [
                'name' => 'Pro Spike Volleyball Dome',
                'slug' => 'pro-spike-volleyball-dome',
                'sport_type' => 'volleyball',
                'description' => 'Shock-absorbing Taraflex flooring engineered to lessen joint impact with professional net height adjustment.',
                'image_url' => 'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 45.00,
                'open_time' => '08:00:00',
                'close_time' => '22:00:00',
                'max_players' => 12,
                'courts' => [
                    ['name' => 'Volleyball Court 1', 'capacity' => 12, 'hourly_rate_override' => null],
                    ['name' => 'Volleyball Court 2', 'capacity' => 12, 'hourly_rate_override' => null],
                ]
            ],
            [
                'name' => 'Royal Lawn Tennis Club',
                'slug' => 'royal-lawn-tennis-club',
                'sport_type' => 'tennis',
                'description' => 'All-weather hard courts and synthetic grass courts with night lighting for evening matches.',
                'image_url' => 'https://images.unsplash.com/photo-1595435934249-5df7ed86e1c0?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 35.00,
                'open_time' => '07:00:00',
                'close_time' => '22:00:00',
                'max_players' => 4,
                'courts' => [
                    ['name' => 'Center Court (Hard Surface)', 'capacity' => 4, 'hourly_rate_override' => 40.00],
                    ['name' => 'Court 2 (Synthetic Turf)', 'capacity' => 4, 'hourly_rate_override' => null],
                ]
            ],
            [
                'name' => 'Precision Table Tennis Lounge',
                'slug' => 'precision-table-tennis-lounge',
                'sport_type' => 'table_tennis',
                'description' => 'ITTF approved tournament tables with rubber safety mats and partition barriers.',
                'image_url' => 'https://images.unsplash.com/photo-1534158914592-062992fbe900?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 15.00,
                'open_time' => '09:00:00',
                'close_time' => '21:00:00',
                'max_players' => 4,
                'courts' => [
                    ['name' => 'Table 1 (Stiga Pro)', 'capacity' => 4, 'hourly_rate_override' => null],
                    ['name' => 'Table 2 (Butterfly Roll-O)', 'capacity' => 4, 'hourly_rate_override' => null],
                    ['name' => 'Table 3 (Butterfly Roll-O)', 'capacity' => 4, 'hourly_rate_override' => null],
                ]
            ],
            [
                'name' => 'Apex Futsal Arena',
                'slug' => 'apex-futsal-arena',
                'sport_type' => 'futsal',
                'description' => 'FIFA certified artificial turf field surrounded by soft safety rebound nets.',
                'image_url' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 55.00,
                'open_time' => '08:00:00',
                'close_time' => '23:00:00',
                'max_players' => 14,
                'courts' => [
                    ['name' => 'Turf Pitch 1', 'capacity' => 14, 'hourly_rate_override' => null],
                    ['name' => 'Turf Pitch 2', 'capacity' => 14, 'hourly_rate_override' => null],
                ]
            ]
        ];

        foreach ($facilitiesData as $fData) {
            $courts = $fData['courts'];
            unset($fData['courts']);

            $facility = Facility::create($fData);

            // Operating Hours
            for ($day = 0; $day < 7; $day++) {
                OperatingHour::create([
                    'facility_id' => $facility->id,
                    'day_of_week' => $day,
                    'open_time' => $facility->open_time,
                    'close_time' => $facility->close_time,
                    'is_closed' => false,
                ]);
            }

            // Courts
            foreach ($courts as $cData) {
                Court::create([
                    'facility_id' => $facility->id,
                    'name' => $cData['name'],
                    'capacity' => $cData['capacity'],
                    'hourly_rate_override' => $cData['hourly_rate_override'],
                    'status' => 'active',
                ]);
            }
        }

        // 3. Holidays
        Holiday::create([
            'name' => 'National Sports & Heritage Day',
            'holiday_date' => Carbon::now()->addDays(12)->toDateString(),
            'is_recurring' => false,
            'note' => 'Facility maintenance & staff holiday.',
        ]);

        // 4. Sample Bookings & Payments
        $users = [$customer1, $customer2, $customer3];
        $allCourts = Court::with('facility')->get();

        $statuses = ['pending', 'approved', 'checked_in', 'completed', 'cancelled'];

        // Past and upcoming bookings
        for ($i = -15; $i <= 10; $i++) {
            $bookingDate = Carbon::today()->addDays($i)->toDateString();
            $court = $allCourts->random();
            $user = $users[array_rand($users)];

            $startHour = rand(9, 19);
            $startTime = sprintf('%02d:00:00', $startHour);
            $endTime = sprintf('%02d:00:00', $startHour + 1);

            $status = $i < 0 ? ($i % 4 == 0 ? 'cancelled' : 'completed') : ($i == 0 ? 'checked_in' : ($i % 2 == 0 ? 'approved' : 'pending'));
            $rate = $court->hourly_rate_override ?? $court->facility->hourly_rate;
            $amount = $rate;

            $code = 'SB-' . date('Ymd', strtotime($bookingDate)) . '-' . strtoupper(Str::random(4));

            $booking = Booking::create([
                'booking_code' => $code,
                'user_id' => $user->id,
                'facility_id' => $court->facility_id,
                'court_id' => $court->id,
                'booking_date' => $bookingDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_hours' => 1.00,
                'hourly_rate' => $rate,
                'subtotal' => $amount,
                'tax_amount' => 0.00,
                'total_amount' => $amount,
                'status' => $status,
                'checked_in_at' => $status === 'checked_in' ? now() : null,
                'cancellation_reason' => $status === 'cancelled' ? 'Schedule conflict' : null,
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'payment_method' => ['cash', 'gcash', 'maya', 'credit_card'][rand(0, 3)],
                'payment_status' => in_array($status, ['approved', 'checked_in', 'completed']) ? 'paid' : ($status === 'cancelled' ? 'refunded' : 'unpaid'),
                'reference_number' => 'REF-' . strtoupper(Str::random(8)),
                'paid_at' => in_array($status, ['approved', 'checked_in', 'completed']) ? now()->subDays(abs($i)) : null,
            ]);
        }
    }
}
