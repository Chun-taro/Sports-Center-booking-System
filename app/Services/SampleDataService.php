<?php

namespace App\Services;

use App\Models\Court;
use App\Models\Facility;
use App\Models\OperatingHour;
use Illuminate\Support\Collection;

class SampleDataService
{
    public static function getFacilities(): Collection
    {
        $items = [
            [
                'id' => 1,
                'name' => 'Apex Badminton Pavilion',
                'slug' => 'apex-badminton-pavilion',
                'sport_type' => 'badminton',
                'description' => 'Professional BWF-grade wooden flooring courts with glare-free LED illumination and air conditioning.',
                'image_url' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 25.00,
                'open_time' => '07:00:00',
                'close_time' => '23:00:00',
                'max_players' => 4,
                'is_active' => true,
                'courts' => [
                    new Court(['id' => 1, 'facility_id' => 1, 'name' => 'Court A (Mat 1)', 'capacity' => 4, 'hourly_rate_override' => 28.00, 'status' => 'active']),
                    new Court(['id' => 2, 'facility_id' => 1, 'name' => 'Court B (Mat 2)', 'capacity' => 4, 'hourly_rate_override' => null, 'status' => 'active']),
                    new Court(['id' => 3, 'facility_id' => 1, 'name' => 'Court C (Mat 3)', 'capacity' => 4, 'hourly_rate_override' => null, 'status' => 'active']),
                ],
            ],
            [
                'id' => 2,
                'name' => 'Grand Slam Basketball Arena',
                'slug' => 'grand-slam-basketball-arena',
                'sport_type' => 'basketball',
                'description' => 'Full-size hardwood maple court equipped with FIBA breakaway rims and electronic scoreboard.',
                'image_url' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 60.00,
                'open_time' => '08:00:00',
                'close_time' => '22:00:00',
                'max_players' => 10,
                'is_active' => true,
                'courts' => [
                    new Court(['id' => 4, 'facility_id' => 2, 'name' => 'Main Championship Court', 'capacity' => 10, 'hourly_rate_override' => 75.00, 'status' => 'active']),
                    new Court(['id' => 5, 'facility_id' => 2, 'name' => 'Practice Half Court', 'capacity' => 6, 'hourly_rate_override' => 40.00, 'status' => 'active']),
                ],
            ],
            [
                'id' => 3,
                'name' => 'Pickleball Social Hub',
                'slug' => 'pickleball-social-hub',
                'sport_type' => 'pickleball',
                'description' => 'High-traction acrylic surface courts optimized for fast-paced pickleball rallies and social leagues.',
                'image_url' => 'https://images.unsplash.com/photo-1599586120429-48281b6f0eca?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 22.00,
                'open_time' => '08:00:00',
                'close_time' => '22:00:00',
                'max_players' => 4,
                'is_active' => true,
                'courts' => [
                    new Court(['id' => 6, 'facility_id' => 3, 'name' => 'Court 1 (Sun Covered)', 'capacity' => 4, 'hourly_rate_override' => null, 'status' => 'active']),
                    new Court(['id' => 7, 'facility_id' => 3, 'name' => 'Court 2 (Sun Covered)', 'capacity' => 4, 'hourly_rate_override' => null, 'status' => 'active']),
                ],
            ],
            [
                'id' => 4,
                'name' => 'Royal Lawn Tennis Club',
                'slug' => 'royal-lawn-tennis-club',
                'sport_type' => 'tennis',
                'description' => 'All-weather hard courts and synthetic grass courts with night lighting for evening matches.',
                'image_url' => 'https://images.unsplash.com/photo-1595435934249-5df7ed86e1c0?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 35.00,
                'open_time' => '07:00:00',
                'close_time' => '22:00:00',
                'max_players' => 4,
                'is_active' => true,
                'courts' => [
                    new Court(['id' => 8, 'facility_id' => 4, 'name' => 'Center Court', 'capacity' => 4, 'hourly_rate_override' => 40.00, 'status' => 'active']),
                    new Court(['id' => 9, 'facility_id' => 4, 'name' => 'Court 2', 'capacity' => 4, 'hourly_rate_override' => null, 'status' => 'active']),
                ],
            ],
            [
                'id' => 5,
                'name' => 'Apex Futsal Arena',
                'slug' => 'apex-futsal-arena',
                'sport_type' => 'futsal',
                'description' => 'FIFA certified artificial turf field surrounded by soft safety rebound nets.',
                'image_url' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&w=1000&q=80',
                'hourly_rate' => 55.00,
                'open_time' => '08:00:00',
                'close_time' => '23:00:00',
                'max_players' => 14,
                'is_active' => true,
                'courts' => [
                    new Court(['id' => 10, 'facility_id' => 5, 'name' => 'Turf Pitch 1', 'capacity' => 14, 'hourly_rate_override' => null, 'status' => 'active']),
                ],
            ],
        ];

        return collect($items)->map(function ($item) {
            $f = new Facility($item);
            $f->id = $item['id'];
            $f->setRelation('courts', collect($item['courts']));
            
            $hours = collect();
            for ($d = 0; $d < 7; $d++) {
                $oh = new OperatingHour([
                    'facility_id' => $f->id,
                    'day_of_week' => $d,
                    'open_time' => $f->open_time,
                    'close_time' => $f->close_time,
                    'is_closed' => false,
                ]);
                $hours->push($oh);
            }
            $f->setRelation('operatingHours', $hours);

            return $f;
        });
    }
}
