<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'kos_name',
        'owner_name',
        'owner_phone',
        'kos_address',
        'gmaps_url',
        'gmaps_embed',
        'location_highlights',
        'landing_content',
        'bank_name',
        'bank_account',
        'bank_holder',
    ];

    protected $casts = [
        'location_highlights' => 'array',
        'landing_content' => 'array',
    ];

    protected $appends = [
        'final_embed_url',
    ];

    /**
     * Resolves and normalizes any user input for Google Maps into a guaranteed working Embed URL.
     * Priority: 1) iframe src  2) /maps/embed URL  3) GPS coords  4) q= URL  5) plain text  6) address fallback
     * Shortlinks (goo.gl) fallback to address — cURL not used (unreliable in sandbox/no-network env).
     */
    public function getFinalEmbedUrlAttribute(): string
    {
        $raw = trim($this->gmaps_embed ?? '');
        $addressFallback = trim($this->kos_address ?? 'Jl. Kaliurang KM 5, Gang Pandega Marta No. 18, Sleman, DI Yogyakarta');

        // 1. If wrapped in <iframe>, extract the src attribute
        if (str_contains($raw, '<iframe')) {
            if (preg_match('/src=["\']([^"\']+)["\']/', $raw, $matches)) {
                $raw = trim($matches[1]);
            }
        }

        // 2. If it's already an embed URL
        if (str_contains($raw, 'google.com/maps/embed') ||
            (str_contains($raw, 'maps.google.com') && str_contains($raw, 'output=embed'))) {
            return $raw;
        }

        // 3. GPS coordinates — most reliable. Supports: -7.7825, 110.3671 | @-7.7825,110.3671
        if (preg_match('/@?(-?\d{1,3}\.\d+),\s*(-?\d{1,3}\.\d+)/', $raw, $coords)) {
            $lat = (float) $coords[1];
            $lng = (float) $coords[2];
            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                // Use ll= (center) + q= (pin marker) + iwloc=B to prevent Google snapping to nearby place
                return 'https://maps.google.com/maps?ll=' . $lat . ',' . $lng
                    . '&q=' . $lat . ',' . $lng
                    . '&t=m&z=18&ie=UTF8&iwloc=B&output=embed';
            }
        }

        // 4. maps.google.com/?q=... with q param
        if (str_contains($raw, 'maps.google.') && str_contains($raw, 'q=')) {
            $parsedQuery = parse_url($raw, PHP_URL_QUERY);
            if ($parsedQuery) {
                parse_str($parsedQuery, $params);
                if (!empty($params['q'])) {
                    if (preg_match('/^(-?\d{1,3}\.\d+),\s*(-?\d{1,3}\.\d+)$/', trim($params['q']), $coords)) {
                        $lat = (float) $coords[1];
                        $lng = (float) $coords[2];
                        return 'https://maps.google.com/maps?ll=' . $lat . ',' . $lng
                            . '&q=' . $lat . ',' . $lng
                            . '&t=m&z=18&ie=UTF8&iwloc=B&output=embed';
                    }
                    return 'https://maps.google.com/maps?q=' . urlencode($params['q']) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
                }
            }
        }

        // 5. Shortlinks — can't be embedded in iframe directly, fallback to address
        if (str_contains($raw, 'maps.app.goo.gl') || str_contains($raw, 'goo.gl/maps')) {
            return 'https://maps.google.com/maps?q=' . urlencode($addressFallback) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
        }

        // 6. Plain text (place name entered by user)
        if (!empty($raw) && !str_starts_with($raw, 'http')) {
            return 'https://maps.google.com/maps?q=' . urlencode($raw) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
        }

        // 7. Fallback: use kos_address — always accurate
        return 'https://maps.google.com/maps?q=' . urlencode($addressFallback) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
    }

    /**
     * Get location highlights with default fallback.
     */
    public function getLocationHighlightsAttribute($value)
    {
        if (!empty($value)) {
            $decoded = is_string($value) ? json_decode($value, true) : $value;
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        return [
            [
                'icon' => '🎓',
                'title' => '3 – 5 Menit ke Kawasan Kampus',
                'desc' => 'Akses sangat dekat ke UGM, UNY, UII, dan berbagai fakultas utama.'
            ],
            [
                'icon' => '☕',
                'title' => '1 – 3 Menit ke Cafe & Sentra Kuliner',
                'desc' => 'Dekat restoran, coffee shop, minimarket 24 jam, dan laundry kiloan.'
            ],
            [
                'icon' => '🏥',
                'title' => '5 Menit ke Rumah Sakit & Fasilitas Umum',
                'desc' => 'Dekat RS Sardjito, RS Panti Rapih, halte Trans Jogja, dan ATM center.'
            ],
            [
                'icon' => '🛡️',
                'title' => 'Lingkungan Aman & Nyaman',
                'desc' => 'Portal keamanan satu pintu (one–gate) dan jalan mobil papasan leluasa.'
            ],
        ];
    }

    /**
     * Get landing page content with default fallbacks.
     */
    public function getLandingContentAttribute($value): array
    {
        $defaults = [
            'hero_title' => "Find Your\nDream Home",
            'hero_desc' => 'Hunian kost modern, bersih, dan aman 24 jam dengan fasilitas AC dingin, kamar mandi pribadi, WiFi fiber 100 Mbps, serta akses lokasi strategis di Sleman, DI Yogyakarta.',
            'hero_image' => 'assets/images/kos_exterior.jpg',
            'hero_btn_text' => 'Jelajahi Kamar',
            'hero_btn_link' => '#kamar',
            
            'bento_tag' => 'Kenyamanan Maksimal',
            'bento_title' => 'Untuk Istirahat & Produktivitas',
            'bento_desc' => 'Setiap jengkal hunian kami rancang dengan seksama untuk memastikan penghuni mendapatkan istirahat berkualitas dan fokus maksimal dalam menuntut ilmu maupun berkarier.',
            'bento_features' => [
                [
                    'icon' => '🚀',
                    'title' => 'Internet Fiber 100 Mbps Tanpa Batas',
                    'desc' => 'Koneksi lancar untuk skripsi, kuliah online, zoom meeting, dan hiburan streaming.'
                ],
                [
                    'icon' => '🔐',
                    'title' => 'Smart Access Mandiri & CCTV 24 Jam',
                    'desc' => 'Bebas jam malam dengan kunci mandiri dan pengawasan kamera CCTV di seluruh lorong.'
                ]
            ],
            'bento_card_tag' => 'FASILITAS UNGGULAN',
            'bento_card_title' => 'Living & Study In Harmony',
            'bento_card_desc' => 'Tersedia dapur bersama komplit dengan kompor dan kulkas, area parkir motor dan mobil luas berpagar aman, serta pembersihan area publik rutin setiap hari.',
            'bento_card_btn' => 'Lihat Unit Kamar',
            'bento_gallery' => [
                'assets/images/hero_scandinavian_room.jpg',
                'assets/images/rooms/room_2.jpg',
                'assets/images/rooms/room_3.jpg',
                'assets/images/kos_exterior.jpg'
            ],

            'founder_name' => $this->owner_name ?? 'Mas Robi',
            'founder_role' => 'Owner & Pengelola Properti',
            'founder_avatar' => 'MR',
            'founder_quote' => 'Kenyamanan, kebersihan, dan keamanan penghuni adalah komitmen utama kami. Kami memastikan setiap anak kos merasakan suasana tinggal yang hangat layaknya di rumah sendiri, dengan fasilitas modern tanpa repot dan dukungan pengelola yang selalu siap membantu kapan pun dibutuhkan.',

            'footer_desc' => 'Hunian kost eksklusif dengan fasilitas lengkap, kamar ber-AC, kamar mandi dalam, WiFi fiber cepat 100 Mbps, dan keamanan 24 jam di kawasan strategis.',
        ];

        if (!empty($value)) {
            $decoded = is_string($value) ? json_decode($value, true) : $value;
            if (is_array($decoded)) {
                return array_merge($defaults, $decoded);
            }
        }

        return $defaults;
    }
}
