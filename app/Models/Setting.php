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
     * Konversi link Google Maps apa pun menjadi URL embed yang berfungsi di iframe.
     *
     * Format input yang didukung (cukup tempel link dari browser):
     *   ✅ https://www.google.com/maps/place/Nama/@-7.7825,110.3671,17z/...
     *   ✅ https://maps.google.com/?q=-7.7825,110.3671
     *   ✅ https://maps.google.com/maps?ll=-7.7825,110.3671&...
     *   ✅ Kode <iframe> embed dari Google Maps > Bagikan > Sematkan peta
     *   ✅ https://maps.google.com/maps/embed?pb=...  (langsung dari "Sematkan")
     *   ⚠️  https://maps.app.goo.gl/... (shortlink) → fallback ke alamat kost
     */
    public function getFinalEmbedUrlAttribute(): string
    {
        $raw = trim($this->gmaps_embed ?? '');
        $addressFallback = trim($this->kos_address ?? 'Jl. Kaliurang KM 5, Gang Pandega Marta No. 18, Sleman, DI Yogyakarta');

        // Langkah 0: Jika input kode <iframe>, ekstrak nilai src
        if (str_contains($raw, '<iframe')) {
            if (preg_match('/src=["\']([^"\']+)["\']/', $raw, $m)) {
                $raw = trim($m[1]);
            }
        }

        // Langkah 1: Sudah berupa URL embed resmi → pakai langsung
        if (str_contains($raw, 'google.com/maps/embed') ||
            (str_contains($raw, 'maps.google.com') && str_contains($raw, 'output=embed'))) {
            return $raw;
        }

        // Langkah 2: Cek apakah shortlink goo.gl -> resolve redirect
        $targetUrl = $raw;
        if (str_contains($raw, 'maps.app.goo.gl') || str_contains($raw, 'goo.gl/maps')) {
            try {
                $ch = curl_init($raw);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
                curl_exec($ch);
                $effective = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                curl_close($ch);
                if (!empty($effective)) {
                    $targetUrl = $effective;
                }
            } catch (\Throwable $e) {}
        }

        // Prioritas A: Cek nama tempat (/place/Nama+Tempat)
        if (preg_match('/place\/([^\/@?#]+)/', $targetUrl, $pm)) {
            $candidate = urldecode(str_replace('+', ' ', $pm[1]));
            if (!empty($candidate) && strtolower($candidate) !== 'dropped pin') {
                return 'https://maps.google.com/maps?q=' . urlencode($candidate) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
            }
        }

        // Prioritas B: Cek koordinat pin asli (!3d<lat>!4d<lng>)
        if (preg_match('/!3d(-?\d{1,3}\.\d+)!4d(-?\d{1,3}\.\d+)/', $targetUrl, $m)) {
            $lat = (float) $m[1];
            $lng = (float) $m[2];
            return 'https://maps.google.com/maps?q=' . $lat . ',' . $lng . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
        }

        // Prioritas C: Cek query parameter q=
        if (str_contains($targetUrl, 'q=')) {
            $qs = parse_url($targetUrl, PHP_URL_QUERY) ?? '';
            parse_str($qs, $params);
            if (!empty($params['q'])) {
                $qVal = trim($params['q']);
                return 'https://maps.google.com/maps?q=' . urlencode($qVal) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
            }
        }

        // Prioritas D: Koordinat kamera (@lat,lng) HANYA jika tidak ada nama tempat & koordinat pin
        if (preg_match('/@(-?\d{1,3}\.\d{4,}),\s*(-?\d{1,3}\.\d{4,})/', $targetUrl, $vm)) {
            $lat = (float) $vm[1];
            $lng = (float) $vm[2];
            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                return 'https://maps.google.com/maps?q=' . $lat . ',' . $lng . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
            }
        }

        // Langkah Terakhir: Fallback ke alamat
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
