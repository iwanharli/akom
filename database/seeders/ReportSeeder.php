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
        ]);
        $report->uuid = (string) Str::uuid();
        $report->save();

        // Stats
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
                'report_id' => $report->id,
                'label' => $s['label'],
                'value' => $s['value'],
                'delta_text' => $s['delta_text'],
                'status_level' => $s['lvl'],
            ]);
        }

        // Section 1
        $sec1 = ReportSection::create([
            'report_id' => $report->id,
            'section_num' => 1,
            'title' => "SITUASI EKONOMI INDONESIA\nDI TENGAH KEKACAUAN GLOBAL",
            'badge_text' => 'ANALISA SITUASI',
            'content_html' => '<p class="lead">Indonesia memasuki 2026 bukan dalam krisis terbuka — tetapi dalam kondisi yang jauh lebih berbahaya: angka-angka makro masih tampak terkendali di permukaan, sementara di bawahnya, struktur fiskal menyempit secara sistemik.</p><p>Pada <strong>28 Februari 2026</strong>, AS dan Israel melancarkan serangan militer besar-besaran ke Iran. Pemimpin tertinggi Iran, Ayatollah Ali Khamenei, dilaporkan tewas. Iran membalas dengan menutup <strong>Selat Hormuz</strong> — jalur vital yang menanggung sekitar <strong>20% pasokan minyak global</strong> dan lebih dari <strong>25% ekspor LNG dunia</strong>.</p><p>Dampaknya langsung terasa. Dalam dua minggu, <strong>harga Brent melesat dari $71/bbl ke $104/bbl</strong> — kenaikan 46%. Ini bukan sekadar angka statistik. Bagi Indonesia, yang merupakan <strong>net importer minyak sejak 2004</strong>, setiap kenaikan $1 per barel setara dengan tekanan tambahan sekitar <strong>Rp6,8 triliun</strong> pada defisit APBN.</p>',
        ]);

        AnalysisItem::create(['section_id' => $sec1->id, 'item_type' => 'timeline', 'heading' => '13 Jun 2025', 'body' => 'Israel serang Teheran. Brent <strong>+13% → $78,50/bbl</strong> dalam hitungan jam.', 'event_date' => '13 Jun 2025']);
        AnalysisItem::create(['section_id' => $sec1->id, 'item_type' => 'timeline', 'heading' => '28 Feb 2026', 'body' => 'AS–Israel serangan besar ke Iran. Khamenei tewas. <strong>Selat Hormuz de facto ditutup.</strong>', 'event_date' => '28 Feb 2026']);
        AnalysisItem::create(['section_id' => $sec1->id, 'item_type' => 'timeline', 'heading' => '3–10 Mar 2026', 'body' => 'Brent <strong>melampaui $100/bbl</strong>. Rupiah menyentuh Rp17.019. Filipina nyatakan darurat energi nasional.', 'event_date' => '3–10 Mar 2026']);

        AnalysisItem::create(['section_id' => $sec1->id, 'item_type' => 'fact_list', 'heading' => 'Konsumsi Energi', 'body' => 'Indonesia konsumsi <strong>1,7 juta barel/hari</strong>, produksi hanya ~600–750 ribu barel/hari']);
        AnalysisItem::create(['section_id' => $sec1->id, 'item_type' => 'fact_list', 'heading' => 'Ketergantungan Impor', 'body' => 'Sekitar <strong>50% kebutuhan minyak</strong> harus dipenuhi dari impor']);
        AnalysisItem::create(['section_id' => $sec1->id, 'item_type' => 'fact_list', 'heading' => 'Rute Hormuz', 'body' => 'Impor minyak yang melewati Selat Hormuz: <strong>18,1%</strong> total impor minyak nasional']);

        ChartData::create([
            'section_id' => $sec1->id,
            'chart_type' => 'line_oil',
            'json_payload' => ['labels' => ['Jan', 'Feb', 'Mar'], 'values' => [71, 71, 104]],
        ]);

        ChartData::create([
            'section_id' => $sec1->id,
            'chart_type' => 'fiscal_stress',
            'json_payload' => [
                ['name' => 'Defisit Ditetapkan', 'label' => 'Rp689T · 2,68% PDB', 'width' => '66%', 'color' => '#2ecc71', 'gradient' => 'linear-gradient(90deg,#2ecc71,#27ae60)'],
                ['name' => 'Skenario Moderat ($90)', 'label' => '~Rp866T · 3,4–3,6% PDB', 'width' => '83%', 'color' => '#e67e22', 'gradient' => 'linear-gradient(90deg,#e67e22,#d35400)'],
                ['name' => 'Skenario Terburuk ($115)', 'label' => 'Rp1.044T · 4,06% PDB 🚨', 'width' => '100%', 'color' => '#e74c3c', 'gradient' => 'linear-gradient(90deg,#e74c3c,#c0392b)'],
            ]
        ]);

        // Section 3
        $sec3 = ReportSection::create([
            'report_id' => $report->id,
            'section_num' => 3,
            'title' => "MENGAPA PRABOWO KEKEH\nMEMPERTAHANKAN MBG?",
            'badge_text' => 'ANALISA INTEL',
            'content_html' => '<p class="lead">Di permukaan, mempertahankan anggaran Rp335 triliun untuk MBG di tengah krisis fiskal tampak seperti keputusan irasional. Di baliknya, terdapat kalkulasi multi-lapis yang jauh lebih kompleks dari sekadar soal gizi anak.</p>',
        ]);

        AnalysisItem::create([
            'section_id' => $sec3->id,
            'item_type' => 'intel_card',
            'tag' => '🎯 DIMENSI POLITIK',
            'heading' => 'MBG adalah Tiket 2029',
            'body' => 'Survei Indikator Politik menunjukkan <strong>72,8% responden puas</strong> terhadap MBG. Memotong MBG sama dengan menyerahkan senjata terbesar kepada oposisi.',
        ]);

        AnalysisItem::create([
            'section_id' => $sec3->id,
            'item_type' => 'intel_card',
            'tag' => '🏭 EKONOMI MULTIPLIER',
            'heading' => 'Mesin Ekonomi Kerakyatan',
            'body' => 'Rp1,2 triliun per hari mengalir ke sistem distribusi pangan lokal. Menciptakan <strong>1,5 juta lapangan kerja baru</strong> di 31.000 dapur.',
        ]);
    }
}
