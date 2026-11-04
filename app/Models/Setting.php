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
        'branches',
        'landing_content',
        'bank_name',
        'bank_account',
        'bank_holder',
        'payment_methods',
        'wifi_list',
        'survey_form_template',
    ];

    protected $casts = [
        'location_highlights' => 'array',
        'branches' => 'array',
        'landing_content' => 'array',
        'payment_methods' => 'array',
        'wifi_list' => 'array',
    ];

    protected $appends = [
        'final_embed_url',
        'active_payment_methods',
        'active_wifi_list',
        'kos_rules',
        'survey_form_template',
    ];

    /**
     * Konversi link Google Maps apa pun menjadi URL embed yang berfungsi di iframe.
     *
     * Format input yang didukung (cukup tempel link dari browser):
     *   https://www.google.com/maps/place/Nama/@-7.7825,110.3671,17z/...
     *   https://maps.google.com/?q=-7.7825,110.3671
     *   https://maps.google.com/maps?ll=-7.7825,110.3671&...
     *   Kode <iframe> embed dari Google Maps > Bagikan > Sematkan peta
     *   https://maps.google.com/maps/embed?pb=...  (langsung dari "Sematkan")
     *    https://maps.app.goo.gl/... (shortlink) → fallback ke alamat kost
     */
    public function getFinalEmbedUrlAttribute(): string
    {
        $raw = trim($this->gmaps_embed ?: ($this->gmaps_url ?? ''));
        $addressFallback = trim($this->kos_address ?? 'Universitas Jember, Jawa Timur');

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
     * Strip any emoji characters recursively from array/string values.
     */
    protected function sanitizeEmojisRecursive($val)
    {
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = $this->sanitizeEmojisRecursive($v);
            }
            return $val;
        }
        if (is_string($val)) {
            return preg_replace('/[\x{1F300}-\x{1F9FF}\x{1FA00}-\x{1FAFF}\x{2600}-\x{27BF}\x{FE0F}\x{200D}\x{2300}-\x{23FF}\x{2B50}\x{2705}\x{274C}\x{2714}\x{2716}\x{2728}]+[ ]?/u', '', $val);
        }
        return $val;
    }

    /**
     * Get location highlights with default fallback.
     */
    public function getLocationHighlightsAttribute($value)
    {
        if (!empty($value)) {
            $decoded = is_string($value) ? json_decode($value, true) : $value;
            if (is_array($decoded) && count($decoded) > 0) {
                return $this->sanitizeEmojisRecursive($decoded);
            }
        }

        return [
            [
                'icon' => '',
                'title' => '3 – 5 Menit ke Kawasan Kampus',
                'desc' => 'Akses sangat dekat ke UGM, UNY, UII, dan berbagai fakultas utama.'
            ],
            [
                'icon' => '',
                'title' => '1 – 3 Menit ke Cafe & Sentra Kuliner',
                'desc' => 'Dekat restoran, coffee shop, minimarket 24 jam, dan laundry kiloan.'
            ],
            [
                'icon' => '',
                'title' => '5 Menit ke Rumah Sakit & Fasilitas Umum',
                'desc' => 'Dekat RS Sardjito, RS Panti Rapih, halte Trans Jogja, dan ATM center.'
            ],
            [
                'icon' => '',
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
            'hero_title' => "Kos Impian\nOrang Jember",
            'hero_desc' => 'Hunian kost modern, bersih, dan aman 24 jam dengan fasilitas AC dingin, kamar mandi pribadi, WiFi fiber 100 Mbps, serta akses lokasi strategis.',
            'hero_image' => 'assets/images/kos_exterior.jpg',
            'hero_btn_text' => 'Jelajahi Kamar',
            'hero_btn_link' => '#kamar',
            
            'bento_tag' => 'Kenyamanan Maksimal',
            'bento_title' => 'Untuk Istirahat & Produktivitas',
            'bento_desc' => 'Setiap jengkal hunian kami rancang dengan seksama untuk memastikan penghuni mendapatkan istirahat berkualitas dan fokus maksimal dalam menuntut ilmu maupun berkarier.',
            'bento_features' => [
                [
                    'icon' => '',
                    'title' => 'Internet Fiber 100 Mbps Tanpa Batas',
                    'desc' => 'Koneksi lancar untuk skripsi, kuliah online, zoom meeting, dan hiburan streaming.'
                ],
                [
                    'icon' => '',
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
                return $this->sanitizeEmojisRecursive(array_merge($defaults, $decoded));
            }
        }

        return $this->sanitizeEmojisRecursive($defaults);
    }

    /**
     * Get payment methods with default fallback list.
     */
    public function getPaymentMethodsAttribute($value): array
    {
        $defaults = [
            [
                'id' => 'pm_bca',
                'name' => 'Transfer Bank BCA',
                'bank_name' => 'Bank Central Asia (BCA)',
                'account_number' => '8830192831',
                'account_holder' => 'Robi Suryanto',
                'type' => 'bank',
                'instructions' => 'Transfer ke rekening resmi BCA di atas dan lampirkan bukti transfer.',
                'is_active' => true,
            ],
            [
                'id' => 'pm_mandiri',
                'name' => 'Transfer Bank Mandiri',
                'bank_name' => 'Bank Mandiri',
                'account_number' => '1370019283100',
                'account_holder' => 'Robi Suryanto',
                'type' => 'bank',
                'instructions' => 'Transfer ke rekening resmi Mandiri di atas dan lampirkan bukti transfer.',
                'is_active' => true,
            ],
            [
                'id' => 'pm_bri',
                'name' => 'Transfer Bank BRI',
                'bank_name' => 'Bank Rakyat Indonesia (BRI)',
                'account_number' => '012301092831502',
                'account_holder' => 'Robi Suryanto',
                'type' => 'bank',
                'instructions' => 'Transfer ke rekening resmi BRI di atas dan lampirkan bukti transfer.',
                'is_active' => true,
            ],
            [
                'id' => 'pm_qris',
                'name' => 'Transfer QRIS All Payment',
                'bank_name' => 'QRIS Resmi Kos Mas Robi',
                'account_number' => 'NMID: ID102026192831',
                'account_holder' => 'Kos Mas Robi / Robi Suryanto',
                'type' => 'qris',
                'instructions' => 'Buka aplikasi m-Banking atau e-Wallet (GoPay, OVO, Dana, ShopeePay), scan kode QRIS atau bayar via NMID di atas.',
                'is_active' => true,
            ],
            [
                'id' => 'pm_cash',
                'name' => 'Tunai (Cash)',
                'bank_name' => 'Tunai Langsung ke Mas Robi',
                'account_number' => 'Bayar Langsung ke Pengelola',
                'account_holder' => 'Mas Robi (Kamar Pengelola Lt. 1)',
                'type' => 'cash',
                'instructions' => 'Serahkan uang tunai sewa kamar langsung kepada Mas Robi di kantor/kamar pengelola lantai 1.',
                'is_active' => true,
            ],
        ];

        if (!empty($value)) {
            $decoded = is_string($value) ? json_decode($value, true) : $value;
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        return $defaults;
    }

    /**
     * Get only active payment methods that should be displayed to tenants.
     */
    public function getActivePaymentMethodsAttribute(): array
    {
        $methods = $this->payment_methods;
        return array_values(array_filter($methods, function ($m) {
            return !empty($m['is_active']);
        }));
    }

    /**
     * Get 2 Branches / Titik Lokasi with rich defaults.
     */
    public function getBranchesAttribute($value): array
    {
        $defaults = [
            'lokasi_1' => [
                'id' => 'lokasi_1',
                'code' => 'Lokasi 1',
                'name' => 'Kos Mas Robi 1 - Kampus Utama',
                'tagline' => 'Hunian Tenang, Nyaman & 3 Menit ke Kampus Utama',
                'address' => $this->kos_address ?: 'Jl. Pandega Marta No. 88, Caturtunggal, Depok, Sleman, D.I. Yogyakarta',
                'gmaps_url' => $this->gmaps_url ?: 'https://maps.google.com/?q=Jl.+Pandega+Marta+No.88+Yogyakarta',
                'gmaps_embed' => $this->gmaps_embed ?: '',
                'phone' => $this->owner_phone ?: '081234567890',
                'room_types' => ['VIP AC (KM Dalam)', 'Deluxe AC', 'Standard Fan (KM Luar)'],
                'facilities' => [
                    'AC Dingin & Hemat Listrik',
                    'Kamar Mandi Dalam & Water Heater',
                    'WiFi Fiber Ultra Cepat 100 Mbps',
                    'Dapur Bersama Komplit (Kulkas & Kompor)',
                    'Parkir Mobil & Motor Luas Beratap',
                    'Keamanan CCTV 24 Jam & One-Gate System',
                    'Rooftop Jemuran & Ruang Santai'
                ],
                'badge' => 'Kampus Utama',
                'color' => '#0a5661',
            ],
            'lokasi_2' => [
                'id' => 'lokasi_2',
                'code' => 'Lokasi 2',
                'name' => 'Kos Mas Robi 2 - Ringroad Exclusive',
                'tagline' => 'Smart Living, Co-Working Space & Fasilitas Modern',
                'address' => 'Jl. Kaliurang KM 12, Sardonoharjo, Ngaglik, Sleman, D.I. Yogyakarta',
                'gmaps_url' => 'https://maps.google.com/?q=Jl.+Kaliurang+KM+12+Yogyakarta',
                'gmaps_embed' => '',
                'phone' => $this->owner_phone ?: '081234567890',
                'room_types' => ['Executive Suite (Balkon)', 'Superior AC Plus', 'Studio Mezzanine'],
                'facilities' => [
                    'Smart Digital Door Lock (Kartu & PIN)',
                    'Balkon Pribadi dengan View Menarik',
                    'Smart Android TV & Meja Kerja Ergonomis',
                    'Co-Working Lounge & Area Diskusi',
                    'Mini Gym & Gazebo Santai Outdoor',
                    'Pantry Bersih & Dispenser Tiap Lantai',
                    'Free Pembersihan Kamar 1x per Minggu'
                ],
                'badge' => 'Ringroad Exclusive',
                'color' => '#0d9488',
            ]
        ];

        if (!empty($value)) {
            $decoded = is_string($value) ? json_decode($value, true) : $value;
            if (is_array($decoded) && !empty($decoded)) {
                return array_merge($defaults, $decoded);
            }
        }

        return $defaults;
    }

    /**
     * Resolve embed URL for a specific branch.
     */
    public function getBranchEmbedUrl(string $branchKey = 'lokasi_1'): string
    {
        $branches = $this->branches;
        $b = $branches[$branchKey] ?? ($branches['lokasi_1'] ?? []);
        $raw = trim($b['gmaps_embed'] ?? ($b['gmaps_url'] ?? ''));
        $addressFallback = trim($b['address'] ?? ($this->kos_address ?? 'Yogyakarta'));

        if (empty($raw) && $branchKey === 'lokasi_1') {
            return $this->final_embed_url;
        }

        if (str_contains($raw, '<iframe') && preg_match('/src=["\']([^"\']+)["\']/', $raw, $m)) {
            $raw = trim($m[1]);
        }
        if (str_contains($raw, 'google.com/maps/embed') || (str_contains($raw, 'maps.google.com') && str_contains($raw, 'output=embed'))) {
            return $raw;
        }
        if (str_contains($raw, 'q=')) {
            $qs = parse_url($raw, PHP_URL_QUERY) ?? '';
            parse_str($qs, $params);
            if (!empty($params['q'])) {
                return 'https://maps.google.com/maps?q=' . urlencode(trim($params['q'])) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
            }
        }
        return 'https://maps.google.com/maps?q=' . urlencode($addressFallback) . '&t=&z=16&ie=UTF8&iwloc=&output=embed';
    }

    /**
     * Get WiFi networks list with sensible defaults for both Kos locations (Lokasi 1 & Lokasi 2).
     * Isinya Nama WiFi, Modem, Lokasi Kos, dan status tayang.
     */
    public function getWifiListAttribute($value): array
    {
        $defaultsLoc1 = [
            [
                'id' => 'wifi_1',
                'location' => 'Lokasi 1',
                'name' => 'KOS_MAS_ROBI_1_5G',
                'modem' => 'Modem Fiber Utama ZTE — Lantai 1 (Kampus Utama)',
                'password' => 'kostnyaman2026',
                'is_active' => true,
            ],
            [
                'id' => 'wifi_2',
                'location' => 'Lokasi 1',
                'name' => 'KOS_MAS_ROBI_1_LT2',
                'modem' => 'Modem Extender TP-Link — Lantai 2 (Kampus Utama)',
                'password' => 'kostnyaman2026',
                'is_active' => true,
            ],
            [
                'id' => 'wifi_3',
                'location' => 'Lokasi 1',
                'name' => 'KOS_MAS_ROBI_1_LOUNGE',
                'modem' => 'Modem Ruang Bersama & Dapur (Kampus Utama)',
                'password' => 'kostnyaman2026',
                'is_active' => false,
            ],
        ];

        $defaultsLoc2 = [
            [
                'id' => 'wifi_4',
                'location' => 'Lokasi 2',
                'name' => 'ROBI_EXCLUSIVE_2_ULTRA5G',
                'modem' => 'Modem Huawei OptiXstar — Lantai 1 & 2 (Ringroad KM 12)',
                'password' => 'exclusive2026',
                'is_active' => true,
            ],
            [
                'id' => 'wifi_5',
                'location' => 'Lokasi 2',
                'name' => 'ROBI_EXCLUSIVE_2_COWORKING',
                'modem' => 'Modem Mesh WiFi 6 — Co-Working & Lounge (Ringroad)',
                'password' => 'exclusive2026',
                'is_active' => true,
            ],
            [
                'id' => 'wifi_6',
                'location' => 'Lokasi 2',
                'name' => 'ROBI_EXCLUSIVE_2_GYM',
                'modem' => 'Modem Area Mini Gym & Gazebo Outdoor (Ringroad)',
                'password' => 'exclusive2026',
                'is_active' => false,
            ],
        ];

        $defaults = array_merge($defaultsLoc1, $defaultsLoc2);

        if (!empty($value)) {
            $decoded = is_string($value) ? json_decode($value, true) : $value;
            if (is_array($decoded) && count($decoded) > 0) {
                $hasLoc2 = false;
                foreach ($decoded as &$item) {
                    if (empty($item['location'])) {
                        $item['location'] = 'Lokasi 1';
                    }
                    if ($item['location'] === 'Lokasi 2') {
                        $hasLoc2 = true;
                    }
                }
                unset($item);

                if (!$hasLoc2) {
                    $decoded = array_merge($decoded, $defaultsLoc2);
                }
                return $decoded;
            }
        }

        return $defaults;
    }

    /**
     * Get only active WiFi networks to show to tenants.
     */
    public function getActiveWifiListAttribute(): array
    {
        $list = $this->wifi_list;
        return array_values(array_filter($list, function ($w) {
            return !empty($w['is_active']);
        }));
    }

    /**
     * Get active WiFi networks filtered by Kos branch location ('Lokasi 1' or 'Lokasi 2').
     */
    public function getActiveWifiForLocation(string $location = 'Lokasi 1'): array
    {
        $list = $this->wifi_list;
        return array_values(array_filter($list, function ($w) use ($location) {
            return !empty($w['is_active']) && (($w['location'] ?? 'Lokasi 1') === $location);
        }));
    }

    /**
     * Get editable Tata Tertib & Peraturan Kos.
     */
    public function getKosRulesAttribute(): array
    {
        $defaultRules = [
            'Pembayaran sewa dilakukan maksimal pada tanggal jatuh tempo setiap bulannya.',
            'Tamu lawan jenis dilarang menginap di dalam kamar. Jam kunjung tamu maksimal pukul 22.00 WIB.',
            'Jaga ketenangan, kebersihan kamar, dan fasilitas bersama (dapur, lorong, area parkir).',
            'Matikan AC, lampu, dan keran air saat meninggalkan kamar dalam waktu lama.',
            'Dilarang keras membawa barang terlarang, senjata tajam, narkoba, atau minuman keras ke lingkungan kos.',
        ];

        $lc = $this->landing_content ?? [];
        if (!empty($lc['kos_rules']) && is_array($lc['kos_rules'])) {
            return array_values($lc['kos_rules']);
        }

        return $defaultRules;
    }

    public const DEFAULT_SURVEY_TEMPLATE = "Halo Admin, saya tertarik untuk menyewa kamar disini \n\nSebelum melanjutkan pemesanan, saya ingin menjadwalkan survey kamar terlebih dahulu sesuai ketentuan. Berikut data diri dan rencana kunjungan saya:\n\n *DATA CALON PENGHUNI*\n• Nama Lengkap : [Tulis Nama Anda]\n• Pekerjaan/Status : [Mahasiswa / Karyawan / dll]\n\n*PILIHAN LOKASI KOST*\n• Lokasi Kost : [Pilih Lokasi 1 / Lokasi 2]\n\n*RENCANA SURVEY KAMAR*\n• Pilihan Kamar : [Tulis Nomor / Tipe Kamar, contoh: Kamar 104 - Lantai 2]\n• Tanggal Survey : [Tulis Tanggal, contoh: Kamis, 5 November 2026]\n• Jam Survey : [Tulis Jam, contoh : 14.00 WIB]\n• Metode Survey : [Datang Langsung / Video Call]\n\nMohon konfirmasinya apakah jadwal tersebut tersedia ya Kak. Terima kasih!";

    /**
     * Get customizable survey & contact form template for prospective tenants.
     */
    public function getSurveyFormTemplateAttribute($value = null): string
    {
        if (!empty($value)) {
            return $value;
        }

        $lc = $this->landing_content ?? [];
        if (!empty($lc['survey_form_template'])) {
            return $lc['survey_form_template'];
        }

        return self::DEFAULT_SURVEY_TEMPLATE;
    }
}
