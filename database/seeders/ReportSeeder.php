<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportStat;
use App\Models\ReportSection;
use App\Models\AnalysisItem;
use App\Models\ChartData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $report = new Report();
        $report->fill([
            'user_id' => 1, // superadmin
            'title' => 'DI TEPI <em>BADAI</em><br>EKONOMI INDONESIA',
            'subtitle' => 'Konflik Iran–AS–Israel, Krisis Energi, APBN di Persimpangan, dan Taruhan Politik Prabowo',
            'alert_text' => '<strong>SIAGA FISKAL:</strong> Harga minyak Brent telah melampaui asumsi APBN 2026 sebesar <strong>40–48%</strong> pasca-serangan AS–Israel ke Iran (28 Feb 2026). Selat Hormuz hampir sepenuhnya tertutup. Rupiah tertekan mendekati Rp17.000–17.019/USD, jauh dari asumsi Rp16.500. Defisit APBN berpotensi melebar ke <strong>Rp1.044 triliun (4,06% PDB)</strong> — melampaui batas hukum 3%.',
            'report_date' => '2026-03-26',
            'classification' => 'TOP SECRET',
            'usd_idr_rate' => 17019.00,
            'brent_oil_price' => 104.50,
            'asumsi_icp' => 70.00,
            'asumsi_kurs' => 16500.00,
            'status' => 'Published',
            'conclusion_html' => '<p><strong>Indonesia berada di persimpangan sejarah fiskal.</strong> Kombinasi perang Iran–AS–Israel, penutupan Selat Hormuz, dan lonjakan harga minyak jauh di atas asumsi APBN telah menempatkan Indonesia pada kondisi tekanan fiskal tertinggi sejak krisis 1998 — tetapi dengan fondasi ekonomi yang jauh lebih kuat.</p><p><strong>Risiko sesungguhnya bukan collapse — tapi erosi bertahap.</strong> Defisit yang melebar, rupiah tertekan, subsidi bengkak, dan investasi yang melambat bisa secara kolektif menurunkan pertumbuhan ke bawah 5% pada semester II 2026. Ini bukan bencana — tapi ini adalah awal dari "decades of lost potential" jika tidak dikelola dengan tepat.</p><p><strong>MBG adalah bet terbesar Prabowo</strong> — bukan karena ia tidak mengerti risikonya, tetapi justru karena ia menghitung bahwa risiko politik dari memotong program ini jauh lebih besar dari risiko fiskalnya. Ini adalah rasionalitas yang berbeda, bukan irasionalitas.</p><p><strong>Jalan keluar yang paling realistis</strong>: negosiasi diplomatik yang meredakan konflik Iran (menurunkan harga minyak kembali ke $75–80), optimalisasi windfall batubara, efisiensi belanja K/L non-prioritas Rp100–150T, dan redesain bertahap MBG agar lebih tepat sasaran — bukan dihapus.</p>',
        ]);
        $report->uuid = (string) Str::uuid();
        $report->save();

        // 1. STATS
        $stats = [
            ['label' => 'Brent Crude (Mar 2026)', 'value' => '~$99', 'delta_text' => 'Asumsi APBN: <strong>$70/bbl</strong> | Gap: <strong>+41%</strong>', 'lvl' => 'danger'],
            ['label' => 'Kurs USD/IDR', 'value' => '17.019', 'delta_text' => 'Asumsi APBN: <strong>Rp16.500</strong> | Gap: <strong>+3,1%</strong>', 'lvl' => 'danger'],
            ['label' => 'Proyeksi Defisit Terburuk', 'value' => '4,06%', 'delta_text' => 'Batas UU: <strong>3% PDB</strong> | Rp1.044 Triliun', 'lvl' => 'danger'],
            ['label' => 'Subsidi Energi (Est. 2026)', 'value' => 'Rp420T+', 'delta_text' => 'Dialokasikan: <strong>Rp210,1T</strong> | Potensi 2x lipat', 'lvl' => 'warning'],
            ['label' => 'Anggaran MBG 2026', 'value' => 'Rp335T', 'delta_text' => 'Naik dari <strong>Rp71T</strong> (2025) | +371%', 'lvl' => 'warning'],
            ['label' => 'Cadangan Devisa', 'value' => '$151,9M', 'delta_text' => 'Setara <strong>6,1 bulan</strong> impor | Cukup sebagai buffer', 'lvl' => 'ok'],
        ];
        foreach ($stats as $s) {
            ReportStat::create([
                'report_id' => $report->id, 'label' => $s['label'], 'value' => $s['value'],
                'delta_text' => $s['delta_text'], 'status_level' => $s['lvl'],
            ]);
        }

        // ════════ SECTION 1 ════════
        $sec1 = ReportSection::create([
            'report_id' => $report->id, 'section_num' => 1,
            'title' => "SITUASI EKONOMI INDONESIA\nDI TENGAH KEKACAUAN GLOBAL",
            'badge_text' => 'ANALISA SITUASI',
            'content_html' => '<p class="lead">Indonesia memasuki 2026 bukan dalam krisis terbuka — tetapi dalam kondisi yang jauh lebih berbahaya: angka-angka makro masih tampak terkendali di permukaan, sementara di bawahnya, struktur fiskal menyempit secara sistemik.</p><p>Pada <strong>28 Februari 2026</strong>, AS dan Israel melancarkan serangan militer besar-besaran ke Iran. Pemimpin tertinggi Iran, Ayatollah Ali Khamenei, dilaporkan tewas. Iran membalas dengan menutup <strong>Selat Hormuz</strong> — jalur vital yang menanggung sekitar 20% pasokan minyak global dan lebih dari 25% ekspor LNG dunia.</p><p>Dampaknya langsung terasa. Dalam dua minggu, harga Brent melesat dari $71/bbl ke $104/bbl — kenaikan 46%. Ini bukan sekadar angka statistik. Bagi Indonesia, yang merupakan net importer minyak sejak 2004, setiap kenaikan $1 per barel setara dengan tekanan tambahan sekitar Rp6,8 triliun pada defisit APBN.</p>
            <div class="prose"><p>Beban fiskal yang sesungguhnya bersifat kumulatif dan saling memperkuat. Tiga komponen belanja besar — <strong>bunga utang (Rp600T), subsidi energi (berpotensi Rp420T+), dan MBG (Rp335T)</strong> — secara bersama mendekati <strong>40% dari total pendapatan negara</strong>.</p></div>
            <table class="scenario-table"><thead><tr><th>Komponen</th><th>Alokasi APBN 2026</th><th>Proyeksi Aktual</th><th>Status</th></tr></thead><tbody>
                <tr><td>Subsidi Energi (BBM+LPG+Listrik)</td><td>Rp210,1 triliun</td><td>Rp380–420+ triliun</td><td class="status-danger">⬆ KRITIS</td></tr>
                <tr><td>Bunga Utang</td><td>Rp600 triliun</td><td>~Rp600 triliun</td><td class="status-warn">⚠ TINGGI</td></tr>
                <tr><td>MBG (Badan Gizi Nasional)</td><td>Rp335 triliun</td><td>~Rp335 triliun</td><td class="status-warn">⚠ RIGID</td></tr>
                <tr><td>Defisit Total (Skenario Terburuk)</td><td>Rp689T (2,68%)</td><td><strong>Rp1.044T (4,06%)</strong></td><td class="status-danger">🚨 LAMPAUI BATAS UU</td></tr>
            </tbody></table>
            <div class="quote-block"><div class="quote-text">"Kalau harga minyak naik ke $92 per barel, kalau tidak melakukan apa-apa, defisit kita naik ke 3,6–3,7 persen dari PDB."</div><div class="quote-attr">— Menteri Keuangan Purbaya Yudhi Sadewa, 6 Maret 2026</div></div>',
        ]);

        // Items Sec 1
        $items1 = [
            ['type' => 'timeline', 'date' => '13 Jun 2025', 'head' => 'Israel serang Teheran', 'body' => 'Brent <strong>+13% → $78,50/bbl</strong> dalam hitungan jam.'],
            ['type' => 'timeline', 'date' => '28 Feb 2026', 'head' => 'AS-Israel serangan besar', 'body' => 'Khamenei tewas. <strong>Selat Hormuz de facto ditutup.</strong>'],
            ['type' => 'timeline', 'date' => '3–10 Mar 2026', 'head' => 'Brent > $100', 'body' => 'Rupiah menyentuh Rp17.019. Filipina nyatakan darurat energi.'],
            ['type' => 'fact_list', 'date' => '', 'head' => 'Konsumsi', 'body' => 'Indonesia konsumsi <strong>1,7 juta barel/hari</strong>, produksi ~600–750k'],
            ['type' => 'fact_list', 'date' => '', 'head' => 'Hormuz', 'body' => 'Impor via Selat Hormuz: <strong>18,1%</strong> total impor minyak nasional'],
            ['type' => 'fact_list', 'date' => '', 'head' => 'Kurs', 'body' => 'Setiap pelemahan Rp100 vs USD → defisit membengkak <strong>Rp0,8 triliun</strong>'],
        ];
        foreach ($items1 as $i) {
            AnalysisItem::create(['section_id' => $sec1->id, 'item_type' => $i['type'], 'event_date' => $i['date'], 'heading' => $i['head'], 'body' => $i['body']]);
        }

        // ═══ DYNAMIC LINE OIL CHART ═══
        ChartData::create([
            'section_id' => $sec1->id,
            'chart_type' => 'line_oil',
            'json_payload' => [
                'title' => 'PERBANDINGAN ASUMSI VS REALITA HARGA BRENT (USD/bbl)',
                'y_range' => ['min' => 40, 'max' => 120],
                'y_step' => 20,
                'asumsi_line' => ['value' => 70, 'label' => 'APBN $70'],
                'data_points' => [
                    ['label' => 'Jan', 'value' => 70],
                    ['label' => 'Feb', 'value' => 71],
                    ['label' => 'Mar', 'value' => 71],
                    ['label' => 'Apr', 'value' => 86],
                    ['label' => 'Mei', 'value' => 95],
                    ['label' => 'Jun', 'value' => 104],
                    ['label' => 'Jul', 'value' => 100],
                    ['label' => 'Agt', 'value' => 95],
                    ['label' => 'Sep', 'value' => 90],
                    ['label' => 'Okt', 'value' => 87],
                ],
            ],
        ]);

        // ═══ DYNAMIC FISCAL STRESS ═══
        ChartData::create([
            'section_id' => $sec1->id,
            'chart_type' => 'fiscal_stress',
            'json_payload' => [
                'title' => 'SKENARIO TEKANAN DEFISIT APBN 2026',
                'batas_marker' => ['position' => '72.7%', 'label' => 'Batas 3%'],
                'scenarios' => [
                    ['name' => 'Defisit Ditetapkan', 'label' => 'Rp689T · 2,68% PDB', 'width' => '66%', 'color' => '#2ecc71', 'gradient' => 'linear-gradient(90deg,#2ecc71,#27ae60)'],
                    ['name' => 'Skenario Moderat', 'label' => '~Rp866T · 3,4–3,6% PDB', 'width' => '83%', 'color' => '#e67e22', 'gradient' => 'linear-gradient(90deg,#e67e22,#d35400)'],
                    ['name' => 'Skenario Terburuk', 'label' => 'Rp1.044T · 4,06% PDB 🚨', 'width' => '100%', 'color' => '#e74c3c', 'gradient' => 'linear-gradient(90deg,#e74c3c,#c0392b)'],
                ],
            ],
        ]);

        // ════════ SECTION 2 ════════
        $sec2 = ReportSection::create([
            'report_id' => $report->id, 'section_num' => 2,
            'title' => "POTENSI INDONESIA\nMENGHADAPI GUNCANGAN INI",
            'badge_text' => 'ANALISA KETAHANAN',
            'content_html' => '<p class="lead">Di balik ancaman yang nyata, Indonesia menyimpan sejumlah kartu yang tidak dimiliki banyak negara berkembang lain. Pertanyaannya bukan apakah Indonesia akan terdampak — tapi seberapa dalam.</p>
            <div class="highlight">KESIMPULAN KETAHANAN: Indonesia berada di posisi "tahan tapi tidak kebal". Fundamental domestik kuat, tetapi tekanan fiskal akumulatif kurs lemah + minyak tinggi memaksa pilihan pahit dalam 6–9 bulan.</div>',
        ]);

        $items2 = [
            ['tag' => '🛡 Kekuatan #1', 'head' => 'Cadangan Devisa', 'body' => 'Cadangan USD 151,9M — setara 6,1 bulan impor. Shock absorber utama stabilisasi Rupiah.'],
            ['tag' => '📈 Kekuatan #2', 'head' => 'PMI Manufaktur', 'body' => 'Stabil di level 53,8. Pertumbuhan Q4 2025 capai 5,39% YoY. Indonesia anomali positif global.'],
            ['tag' => '⚡ Kekuatan #3', 'head' => 'Windfall Batubara', 'body' => 'Harga naik ~15% sejak krisis Hormuz. Mengkompensasi sebagian tekanan fiskal.'],
            ['tag' => '🌐 Kekuatan #4', 'head' => 'Diversifikasi Rute', 'body' => 'Hanya 18,1% impor via Hormuz. Mayoritas BBM dari Singapura/Malaysia (rute selatan).'],
            ['tag' => '⚠ Kelemahan #1', 'head' => 'Net-Importer', 'body' => 'Produksi 600-750k barel vs konsumsi 1,7jt. Defisit struktural persisten.'],
            ['tag' => '⚠ Kelemahan #2', 'head' => 'Ruang Fiskal', 'body' => 'Bunga utang Rp600T (19% pendapatan). Ruang fleksibilitas respons krisis kian sempit.'],
            ['tag' => '⚠ Kelemahan #3', 'head' => 'Inflasi Pangan', 'body' => 'Kenaikan energi merembet ke logistik dan tarif transport harian kelompok menengah-bawah.'],
            ['tag' => '💡 Peluang', 'head' => 'Middle Power', 'body' => 'Posisi independen buka akses negosiasi jalur energi alternatif dengan berbagai blok global.'],
        ];
        foreach ($items2 as $i) {
            AnalysisItem::create(['section_id' => $sec2->id, 'item_type' => 'intel_card', 'tag' => $i['tag'], 'heading' => $i['head'], 'body' => $i['body']]);
        }

        // ════════ SECTION 3 ════════
        $sec3 = ReportSection::create([
            'report_id' => $report->id, 'section_num' => 3,
            'title' => "MENGAPA PRABOWO KEKEH\nMEMPERTAHANKAN MBG?",
            'badge_text' => 'ANALISA INTEL',
            'content_html' => '<p class="lead">Di permukaan, mempertahankan anggaran Rp335 triliun untuk MBG tampak irasional. Di baliknya, terdapat kalkulasi multi-lapis yang jauh lebih kompleks.</p>
            <div class="quote-block"><div class="quote-text">"Saya akan bertahan sedapat mungkin. Daripada uang-uang dikorupsi, lebih baik rakyat saya bisa makan."</div><div class="quote-attr">— Presiden Prabowo Subianto, 22 Maret 2026</div></div>',
        ]);

        $items3 = [
            ['tag' => '🎯 Politik', 'head' => 'Tiket 2029', 'body' => '72,8% responden puas terhadap MBG. Memotong MBG sama dengan menyerahkan senjata terbesar kepada oposisi.'],
            ['tag' => '🏭 Multiplier', 'head' => 'Dapur Lokal', 'body' => 'Rp1,2T/hari mengalir ke distribusi pangan lokal. Ciptakan 1,5jt lapangan kerja baru di 31.000 dapur.'],
            ['tag' => '🧠 Human Capital', 'head' => 'Investasi Neraca', 'body' => 'Stunting adalah masalah produktivitas 20 thn depan. MBG adalah investasi jangka panjang SDM.'],
            ['tag' => '🛡 Anti-Korupsi', 'head' => 'Penguncian Anggaran', 'body' => 'Strategi pengalihan uang negara ke jalur distribusi terukur daripada kas gelap kementerian.'],
            ['tag' => '🔍 Kontrak Sosial', 'head' => 'Legitimasi Moral', 'body' => 'Bukti konkret negara hadir bagi keluarga miskin. Fondasi stabilitas politik era Prabowo.'],
        ];
        foreach ($items3 as $i) {
            AnalysisItem::create(['section_id' => $sec3->id, 'item_type' => 'intel_card', 'tag' => $i['tag'], 'heading' => $i['head'], 'body' => $i['body']]);
        }

        // ═══ DYNAMIC MBG EVOLUTION CHART ═══
        ChartData::create([
            'section_id' => $sec3->id,
            'chart_type' => 'mbg_evolution',
            'json_payload' => [
                'title' => 'ESKALASI ANGGARAN MBG (TRILIUN RUPIAH)',
                'bars' => [
                    ['label' => 'APBN 2025', 'sub_label' => 'Awal', 'value_label' => 'Rp71T', 'color' => '#e8a020', 'opacity' => 0.7, 'height_pct' => 24],
                    ['label' => 'APBN 2025', 'sub_label' => '+Tambahan', 'value_label' => 'Rp171T', 'color' => '#e8a020', 'opacity' => 0.8, 'height_pct' => 51],
                    ['label' => 'KEM-PPKF', 'sub_label' => '2026 Usulan', 'value_label' => 'Rp217T', 'color' => '#e67e22', 'opacity' => 0.8, 'height_pct' => 64],
                    ['label' => 'APBN 2026', 'sub_label' => 'FINAL (+53,8%)', 'value_label' => 'Rp335T', 'color' => '#e74c3c', 'opacity' => 1.0, 'height_pct' => 100],
                ],
                'callout' => ['text' => '+371%', 'sub_text' => 'dalam 1 tahun'],
            ],
        ]);
    }
}
