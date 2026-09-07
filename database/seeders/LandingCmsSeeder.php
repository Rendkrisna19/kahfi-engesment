<?php

namespace Database\Seeders;

use App\Models\LandingItem;
use App\Models\LandingSetting;
use Illuminate\Database\Seeder;

class LandingCmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Landing Settings
        $settings = [
            // General & Brand
            'brand_name' => ['Kahfi Engagement', 'general'],
            'brand_tagline' => ['Solusi Distribusi Konten & Campaign Creator Skala Besar', 'general'],
            'contact_whatsapp' => ['6281234567890', 'general'],
            'contact_email' => ['halo@kahfiengagement.com', 'general'],
            'floating_wa_text' => ['Konsultasi Campaign Sekarang', 'general'],

            // Hero Section
            'hero_badge' => ['🔥 Ekosistem Distribusi Organik No. 1 di Indonesia', 'hero'],
            'hero_headline' => ['Banjir Jutaan Views & Ribuan Followers Tanpa Iklan Mahal', 'hero'],
            'hero_subheadline' => ['Tingkatkan brand awareness, trust, dan penjualan Anda secara eksponensial melalui jaringan ratusan kreator organik terverifikasi. Terpantau realtime dalam 1 dashboard.', 'hero'],
            'hero_primary_cta_text' => ['Mulai Campaign Sekarang', 'hero'],
            'hero_primary_cta_url' => ['https://wa.me/6281234567890?text=Halo%20Kahfi%20Engagement,%20saya%20ingin%20konsultasi%20campaign%20organik', 'hero'],
            'hero_secondary_cta_text' => ['Lihat Portofolio Kami', 'hero'],
            'hero_secondary_cta_url' => ['#portofolio', 'hero'],
            'hero_stats_creators' => ['500+', 'hero'],
            'hero_stats_creators_label' => ['Kreator Aktif', 'hero'],
            'hero_stats_views' => ['150M+', 'hero'],
            'hero_stats_views_label' => ['Total Views Terdistribusi', 'hero'],
            'hero_stats_brands' => ['120+', 'hero'],
            'hero_stats_brands_label' => ['Brand & Bisnis Puas', 'hero'],

            // Kenapa Kamu Harus Gabung
            'why_join_badge' => ['KEUNGGULAN KAMI', 'why_join'],
            'why_join_title' => ['Kenapa Kamu Harus Gabung Kahfi Engagement?', 'why_join'],
            'why_join_subtitle' => ['Kami merevolusi cara brand berpromosi: dari iklan berbayar yang kian mahal ke gelombang konten organik yang autentik dan dipercaya audiens.', 'why_join'],

            // Bukan Untuk Semua Orang
            'not_for_badge' => ['KUALIFIKASI KLIEN', 'not_for'],
            'not_for_title' => ['Bukan Untuk Semua Orang', 'not_for'],
            'not_for_subtitle' => ['Jangan Daftar Kalau Kamu:', 'not_for'],
            'not_for_description' => ['Kami hanya bekerja dengan brand dan pebisnis yang serius ingin membangun aset awareness jangka panjang dan siap scale up kapasitas penjualan.', 'not_for'],

            // Portofolio Section
            'portfolio_badge' => ['HASIL NYATA', 'portfolio'],
            'portfolio_title' => ['Portofolio Kami', 'portfolio'],
            'portfolio_subtitle' => ['Bukti distribusi video organik dengan jutaan views di TikTok dan Instagram Reels tanpa biaya ads.', 'portfolio'],
            'portfolio_total_views' => ['150.000.000+', 'portfolio'],
            'portfolio_views_label' => ['Total Views Berhasil Kami Hasilkan untuk Klien', 'portfolio'],

            // Client / Partners Section
            'client_badge' => ['KLIEN KAMI', 'client'],
            'client_title' => ['Brand Yang Telah Mempercayai Kami', 'client'],
            'client_subtitle' => ['Ratusan brand dari sektor F&B, Fashion, Beauty, Gadget, hingga Jasa telah tumbuh bersama kami.', 'client'],

            // Bagaimana Kami Bekerja
            'how_badge' => ['PROSES KERJA', 'how_it_works'],
            'how_title' => ['Bagaimana Kami Bekerja', 'how_it_works'],
            'how_subtitle' => ['Sistem 4 langkah teruji yang memastikan konten Anda viral tepat sasaran tanpa membuang waktu dan energi Anda.', 'how_it_works'],

            // Analisis Efisiensi
            'efficiency_badge' => ['PERBANDINGAN BIAYA', 'efficiency'],
            'efficiency_title' => ['ANALISIS EFISIENSI: DISTRIBUSI ORGANIK VS AGENCY', 'efficiency'],
            'efficiency_subtitle' => ['Bandingkan sendiri mengapa brand-brand cerdas mulai beralih dari bakar uang iklan konvensional ke distribusi engagement organik bersama Kahfi Engagement.', 'efficiency'],
            'efficiency_footnote' => ['*Perhitungan berdasarkan data rata-rata CPM industri Q1 2026. Konten organik tetap aktif selamanya di feed kreator (Evergreen Traffic).', 'efficiency'],

            // Siap Banjir Jutaan Views (CTA Section)
            'cta_badge' => ['AMBIL KESEMPATAN', 'cta'],
            'cta_title' => ['Siap Banjir Jutaan Views?', 'cta'],
            'cta_subtitle' => ['Slot campaign bulanan kami dibatasi agar setiap brand mendapatkan alokasi kreator terbaik dan engagement maksimal. Hubungi kami sekarang sebelum kuota penuh.', 'cta'],
            'cta_button_text' => ['Amankan Slot Campaign Anda Sekarang', 'cta'],
            'cta_button_url' => ['https://wa.me/6281234567890?text=Halo%20Admin%20Kahfi%20Engagement,%20saya%20siap%20banjir%20views!', 'cta'],

            // Bukti Payment
            'payment_badge' => ['TRANSPARANSI KREATOR', 'payment'],
            'payment_title' => ['Bukti Payment', 'payment'],
            'payment_subtitle' => ['Bukti nyata komitmen kami membayar ratusan kreator tepat waktu, transparan, dan profesional.', 'payment'],
        ];

        foreach ($settings as $key => [$value, $group]) {
            LandingSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }

        // 2. Clear old landing items if any
        LandingItem::truncate();

        // 3. Why Join Items
        $whyJoins = [
            [
                'title' => 'Distribusi Organik Skala Besar',
                'description' => 'Satu campaign langsung dieksekusi oleh puluhan hingga ratusan kreator dalam waktu bersamaan untuk menciptakan efek domino FOMO.',
                'sort_order' => 1,
            ],
            [
                'title' => 'Algoritma & FYP Friendly',
                'description' => 'Strategi hook and loop teruji yang disesuaikan dengan pola algoritma terbaru TikTok dan Instagram Reels tanpa risiko shadowban.',
                'sort_order' => 2,
            ],
            [
                'title' => 'Monitoring Realtime Dalam 1 Dashboard',
                'description' => 'Pantau pertumbuhan views, likes, comments, dan performa setiap link kreator langsung secara live di dashboard Kahfi Engagement.',
                'sort_order' => 3,
            ],
            [
                'title' => 'Biaya Jauh Lebih Murah Dari Paid Ads',
                'description' => 'Dapatkan CPM serendah Rp 3.000 - Rp 8.000 per seribu views, hemat biaya promosi hingga 70% dibanding biaya pasang iklan konvensional.',
                'sort_order' => 4,
            ],
            [
                'title' => 'Audiens Asli & Konversi Lebih Tinggi',
                'description' => 'Audiens Indonesia percaya rekomendasi video kreator asli dibanding banner iklan bersponsor yang sering di-skip begitu saja.',
                'sort_order' => 5,
            ],
            [
                'title' => 'Aset Konten Evergreen Selamanya',
                'description' => 'Berbeda dengan iklan berbayar yang lenyap saat budget habis, video organik kreator kami tetap tayang dan mendatangkan traffic selamanya.',
                'sort_order' => 6,
            ],
        ];
        foreach ($whyJoins as $item) {
            LandingItem::create(array_merge($item, ['type' => 'why_join', 'is_active' => true]));
        }

        // 4. Bukan Untuk Semua Orang Items
        $notFors = [
            [
                'title' => 'Ingin Hasil Instan Dalam 1 Jam',
                'description' => 'Distribusi video organik membutuhkan waktu 24 - 72 jam bagi algoritma untuk memetakan penonton yang tepat dan meledakkan views.',
                'sort_order' => 1,
            ],
            [
                'title' => 'Tidak Memiliki Produk / Jasa yang Siap',
                'description' => 'Kami bertugas mendatangkan traffic masif. Pastikan produk atau landing page Anda siap menerima lonjakan order dan DM pelanggan.',
                'sort_order' => 2,
            ],
            [
                'title' => 'Mencari Bot / Fake Views & Engagement',
                'description' => 'Kami 100% menggunakan interaksi manusia asli. Kami menolak manipulasi bot yang membahayakan akun bisnis jangka panjang.',
                'sort_order' => 3,
            ],
            [
                'title' => 'Menolak Fleksibilitas Kreativitas Kreator',
                'description' => 'Kreator tahu gaya bahasa terbaik untuk audiens mereka. Pendekatan terlalu kaku seringkali membunuh engagement organik.',
                'sort_order' => 4,
            ],
        ];
        foreach ($notFors as $item) {
            LandingItem::create(array_merge($item, ['type' => 'not_for', 'is_active' => true]));
        }

        // 5. Bagaimana Kami Bekerja (How It Works)
        $howSteps = [
            [
                'subtitle' => 'LANGKAH 01',
                'title' => 'Penyusunan Brief & Angle Hook',
                'description' => 'Tim kami bersama Anda merumuskan poin unik produk, hook 3 detik pertama, dan call-to-action yang memancing rasa penasaran audiens.',
                'sort_order' => 1,
            ],
            [
                'subtitle' => 'LANGKAH 02',
                'title' => 'Distribusi ke Creator Terpilih',
                'description' => 'Brief didistribusikan ke ratusan kreator afiliasi terdaftar yang memiliki demografi followers selaras dengan niche bisnis Anda.',
                'sort_order' => 2,
            ],
            [
                'subtitle' => 'LANGKAH 03',
                'title' => 'Produksi & Golden Hour Posting',
                'description' => 'Kreator memproduksi video autentik dan menayangkannya secara serentak di jam-jam tayang dengan interaksi tertinggi.',
                'sort_order' => 3,
            ],
            [
                'subtitle' => 'LANGKAH 04',
                'title' => 'Monitoring Live di Dashboard',
                'description' => 'Sistem bot kami otomatis mengumpulkan data views, likes, dan shares. Anda dapat mengunduh laporan PDF/Excel kapan saja.',
                'sort_order' => 4,
            ],
        ];
        foreach ($howSteps as $item) {
            LandingItem::create(array_merge($item, ['type' => 'how_step', 'is_active' => true]));
        }

        // 6. Analisis Efisiensi Cards
        $efficiencyCards = [
            [
                'title' => 'Meta Ads (FB & IG)',
                'subtitle' => 'Iklan Berbayar Standar',
                'description' => 'Metode lama yang semakin hari semakin mahal dengan tingkat persaingan auction yang sangat ketat.',
                'extra_meta' => [
                    'cost_range' => 'Rp 5.000.000 - Rp 15.000.000',
                    'cpm' => 'Rp 25.000 - Rp 50.000',
                    'highlight_label' => 'Bakar Uang Iklan',
                    'pros' => [
                        'Targeting minat spesifik',
                        'Bisa langsung direct link ke website',
                    ],
                    'cons' => [
                        'Views berhenti seketika saat budget saldo habis',
                        'Audiens makin skeptis dengan label "Bersponsor"',
                        'Sering terjadi pembatasan / ban akun iklan sepihak',
                        'Biaya CPM meroket naik tiap tahun',
                    ],
                    'is_winner' => false,
                ],
                'sort_order' => 1,
            ],
            [
                'title' => 'TikTok Ads Manager',
                'subtitle' => 'Spark Ads & In-Feed',
                'description' => 'Bagus untuk format vertikal namun membutuhkan biaya produksi dan budget harian yang tidak sedikit.',
                'extra_meta' => [
                    'cost_range' => 'Rp 4.000.000 - Rp 12.000.000',
                    'cpm' => 'Rp 20.000 - Rp 45.000',
                    'highlight_label' => 'Biaya Cepat Habis',
                    'pros' => [
                        'Format video layar penuh',
                        'Bisa diintegrasikan dengan TikTok Shop',
                    ],
                    'cons' => [
                        'Penonton langsung skip jika tercium aroma jualan kaku',
                        'Minim loyalitas audiens terhadap profil bisnis Anda',
                        'Perlu biaya fee agency iklan tambahan yang mahal',
                        'Retensi video cenderung sangat rendah',
                    ],
                    'is_winner' => false,
                ],
                'sort_order' => 2,
            ],
            [
                'title' => 'Kahfi Engagement',
                'subtitle' => 'Distribusi Organik Terpadu',
                'description' => 'Solusi masa depan: mendominasi FYP melalui puluhan kreator nyata dengan biaya paling efisien.',
                'extra_meta' => [
                    'cost_range' => 'Mulai Rp 1.500.000-an',
                    'cpm' => 'Rp 3.000 - Rp 8.000',
                    'highlight_label' => 'PILIHAN TERBAIK & PALING EFISIEN',
                    'badge' => '⭐ REKOMENDASI PEMENANG',
                    'pros' => [
                        'Hemat biaya promosi hingga 75% dibanding paid ads',
                        'Konten tetap tayang selamanya (Traffic Evergreen)',
                        'Audiens lebih percaya rekomendasi kreator manusia asli',
                        'Membangun social proof & FOMO secara alami',
                        'Terpantau realtime di Dashboard Analitik terpadu',
                        'Bebas risiko banned akun iklan',
                    ],
                    'cons' => [],
                    'is_winner' => true,
                ],
                'sort_order' => 3,
            ],
        ];
        foreach ($efficiencyCards as $item) {
            LandingItem::create(array_merge($item, ['type' => 'efficiency_card', 'is_active' => true]));
        }

        // 7. CTA Chips
        $chips = [
            '⚡ Slot Terbatas Tiap Bulan',
            '📊 Dashboard Pantau Realtime',
            '🎯 Garansi Distribusi views',
            '🤝 Ratusan Kreator Aktif',
            '✨ Cocok Segala Niche Bisnis',
        ];
        foreach ($chips as $idx => $chip) {
            LandingItem::create([
                'type' => 'cta_chip',
                'title' => $chip,
                'sort_order' => $idx + 1,
                'is_active' => true,
            ]);
        }

        // 8. Client Logos (Sample entries with modern SVG badges/names)
        $clients = [
            ['title' => 'GlowUp Skincare', 'subtitle' => 'Beauty & Skincare Brand', 'image' => null, 'sort_order' => 1],
            ['title' => 'Kopi Senja Utama', 'subtitle' => 'F&B Franchise Network', 'image' => null, 'sort_order' => 2],
            ['title' => 'UrbanWear ID', 'subtitle' => 'Streetwear Fashion Label', 'image' => null, 'sort_order' => 3],
            ['title' => 'GadgetStore Official', 'subtitle' => 'Retailer Elektronik & Aksesoris', 'image' => null, 'sort_order' => 4],
            ['title' => 'EduSkill Indonesia', 'subtitle' => 'Platform Kursus Digital', 'image' => null, 'sort_order' => 5],
            ['title' => 'Hijab Modern Co.', 'subtitle' => 'Busana Muslimah Premium', 'image' => null, 'sort_order' => 6],
            ['title' => 'HealthySnack Nusantara', 'subtitle' => 'F&B Kuliner Sehat', 'image' => null, 'sort_order' => 7],
            ['title' => 'Parfume Luxe ID', 'subtitle' => 'Wewangian Eksklusif', 'image' => null, 'sort_order' => 8],
        ];
        foreach ($clients as $client) {
            LandingItem::create(array_merge($client, ['type' => 'client_logo', 'is_active' => true]));
        }

        // 9. Portfolio Items
        $portfolios = [
            [
                'title' => 'Campaign Viral Serum Brightening',
                'subtitle' => 'Kategori: Beauty & Skincare',
                'description' => 'Mendistribusikan 45 video creator serentak dalam 3 hari, menghasilkan lonjakan pencarian brand dan out-of-stock di marketplace.',
                'extra_meta' => ['views' => '4.8M Views', 'creators' => '45 Kreator', 'platform' => 'TikTok'],
                'sort_order' => 1,
            ],
            [
                'title' => 'Launching Menu Baru Coffee Shop',
                'subtitle' => 'Kategori: F&B Lifestyle',
                'description' => 'Review rasa autentik oleh food vloggers lokal, antrean pengunjung meningkat hingga 300% pada minggu pembukaan.',
                'extra_meta' => ['views' => '2.6M Views', 'creators' => '28 Kreator', 'platform' => 'Instagram Reels'],
                'sort_order' => 2,
            ],
            [
                'title' => 'Drop Koleksi Oversize T-Shirt',
                'subtitle' => 'Kategori: Streetwear Fashion',
                'description' => 'OOTD transition video yang trending dengan audio viral, total 1.200 order tercatat dalam 48 jam pertama.',
                'extra_meta' => ['views' => '3.4M Views', 'creators' => '35 Kreator', 'platform' => 'TikTok'],
                'sort_order' => 3,
            ],
            [
                'title' => 'Review TWS Noise Cancelling',
                'subtitle' => 'Kategori: Gadget & Tech',
                'description' => 'Eksperimen uji suara ekstrem di tempat bising yang membuktikan kualitas produk secara visual dan meyakinkan.',
                'extra_meta' => ['views' => '1.9M Views', 'creators' => '20 Kreator', 'platform' => 'TikTok'],
                'sort_order' => 4,
            ],
            [
                'title' => 'Promo Ramadhan Hampers Kue',
                'subtitle' => 'Kategori: Food & Gift',
                'description' => 'Unboxing video elegan menyentuh momen emosional keluarga menjelang hari raya, pesanan pre-order full booked.',
                'extra_meta' => ['views' => '5.2M Views', 'creators' => '50 Kreator', 'platform' => 'TikTok & Reels'],
                'sort_order' => 5,
            ],
            [
                'title' => 'Webinar Karir & Skill Digital',
                'subtitle' => 'Kategori: EduTech',
                'description' => 'Storytelling tips karir yang relate dengan fresh graduate, menghasilkan 3.400+ pendaftaran peserta baru.',
                'extra_meta' => ['views' => '2.1M Views', 'creators' => '22 Kreator', 'platform' => 'TikTok'],
                'sort_order' => 6,
            ],
        ];
        foreach ($portfolios as $portfolio) {
            LandingItem::create(array_merge($portfolio, ['type' => 'portfolio', 'is_active' => true]));
        }

        // 10. Bukti Payment Items
        $payments = [
            [
                'title' => 'Pembayaran Batch Mingguan Creator',
                'subtitle' => 'Disalurkan ke 42 Creator',
                'description' => 'Total transfer komisi dan fee campaign sebesar Rp 28.500.000 sukses diterima seluruh kreator mitra.',
                'extra_meta' => ['amount' => 'Rp 28.500.000', 'date' => 'Maret 2026', 'status' => 'Lunas / Berhasil'],
                'sort_order' => 1,
            ],
            [
                'title' => 'Fee Campaign Skincare Season 1',
                'subtitle' => 'Disalurkan ke 30 Creator',
                'description' => 'Pelunasan fee review video TikTok berbayar senilai Rp 19.800.000 tepat waktu di hari H publikasi.',
                'extra_meta' => ['amount' => 'Rp 19.800.000', 'date' => 'Februari 2026', 'status' => 'Lunas / Berhasil'],
                'sort_order' => 2,
            ],
            [
                'title' => 'Bonus Performa Views Tertinggi',
                'subtitle' => 'Top 5 Creator Viral',
                'description' => 'Apresiasi bonus pencapaian 1M+ views bagi kreator dengan engagement tertinggi total Rp 7.500.000.',
                'extra_meta' => ['amount' => 'Rp 7.500.000', 'date' => 'Februari 2026', 'status' => 'Lunas / Berhasil'],
                'sort_order' => 3,
            ],
            [
                'title' => 'Payout Afiliasi & Engagement Bulanan',
                'subtitle' => 'Disalurkan ke 85 Creator',
                'description' => 'Distribusi rutin penghasilan kreator komunitas Kahfi Engagement senilai Rp 54.200.000 tanpa potongan tersembunyi.',
                'extra_meta' => ['amount' => 'Rp 54.200.000', 'date' => 'Januari 2026', 'status' => 'Lunas / Berhasil'],
                'sort_order' => 4,
            ],
        ];
        foreach ($payments as $payment) {
            LandingItem::create(array_merge($payment, ['type' => 'payment_proof', 'is_active' => true]));
        }
    }
}
