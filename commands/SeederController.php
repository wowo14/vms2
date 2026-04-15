<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\PaketPengadaan;
use app\models\Dpp;
use app\models\PenilaianPenyedia;
use app\models\Penyedia;
use app\models\Pegawai;
use app\models\Unit;
use app\models\Setting;
use app\models\User;

class SeederController extends Controller
{
    public function actionDebug()
    {
        $ppk = Setting::find()->where(['type' => 'evaluasi_suplier_ppk', 'active' => 1])->one();
        $pejabat = Setting::find()->where(['type' => 'evaluasi_suplier_pejabat', 'active' => 1])->one();
        
        echo "PPK Found: " . ($ppk ? "YES" : "NO") . "\n";
        if ($ppk) echo "PPK Value: " . $ppk->value . "\n\n";
        
        echo "Pejabat Found: " . ($pejabat ? "YES" : "NO") . "\n";
        if ($pejabat) echo "Pejabat Value: " . $pejabat->value . "\n";
        
        return ExitCode::OK;
    }

    public function actionPenilaian()
    {
        // 1. Hapus data seeder sebelumnya
        echo "Clearing PenilaianPenyedia table...\n";
        PenilaianPenyedia::deleteAll();
        
        // Hapus paket buatan seeder jika masih ada
        $oldPakets = PaketPengadaan::find()->where(['like', 'nomor', 'PAKET-20'])->all();
        foreach ($oldPakets as $p) {
            $dpp = Dpp::findOne(['paket_id' => $p->id]);
            if ($dpp) {
                $dpp->delete();
            }
            $p->delete();
        }
        echo "Old seed data deleted.\n";

        $kriteria_config = [];
        $settings_ppk = Setting::find()->where(['type' => 'evaluasi_suplier_ppk', 'active' => 1])->one();
        $settings_pejabat = Setting::find()->where(['type' => 'evaluasi_suplier_pejabat', 'active' => 1])->one();

        if ($settings_ppk) {
            $val = json_decode($settings_ppk->value, true);
            if (isset($val['kriteria'])) {
                foreach ($val['kriteria'] as $k) {
                    // Coba 'description' lalu 'desc' sebagai fallback jika ada deviasi struktur
                    $desc_data = $k['description'] ?? ($k['desc'] ?? []);
                    if (!empty($desc_data)) {
                        $kriteria_config[] = [
                            'name' => $k['name'], 
                            'scores' => array_keys($desc_data), 
                            'source' => 'ppk'
                        ];
                    }
                }
            }
        }
        if ($settings_pejabat) {
            $val = json_decode($settings_pejabat->value, true);
            if (isset($val['kriteria'])) {
                foreach ($val['kriteria'] as $k) {
                    $desc_data = $k['description'] ?? ($k['desc'] ?? []);
                    if (!empty($desc_data)) {
                        $kriteria_config[] = [
                            'name' => $k['name'], 
                            'scores' => array_keys($desc_data), 
                            'source' => 'pejabat'
                        ];
                    }
                }
            }
        }

        if (empty($kriteria_config)) {
             echo "Error: No evaluation criteria found in 'setting' table (using Setting::find()). Seeding aborted.\n";
             return ExitCode::UNSPECIFIED_ERROR;
        }

        // 3. Gunakan data PAKET dan DPP yang sudah ada
        // Kita cari paket yang sudah ada pemenangnya
        $packets = PaketPengadaan::find()->where(['not', ['pemenang' => null]])->all();
        
        if (empty($packets)) {
            echo "No existing packets with winners found. Cannot seed penilaian without packets.\n";
            return ExitCode::UNSPECIFIED_ERROR;
        }

        foreach ($packets as $paket) {
            $dpp = Dpp::findOne(['paket_id' => $paket->id]);
            if (!$dpp) continue;

            $provider = Penyedia::findOne($paket->pemenang);
            if (!$provider) continue;

            // Cek apakah sudah ada penilaian untuk dpp ini
            $exist = PenilaianPenyedia::findOne(['dpp_id' => $dpp->id]);
            if ($exist) continue;

            echo "Seeding penilaian for: " . $paket->nama_paket . " ($paket->tahun_anggaran)\n";

            $penilaian = new PenilaianPenyedia();
            $penilaian->dpp_id = $dpp->id;
            $unit = Unit::findOne($paket->unit);
            $penilaian->unit_kerja = $unit ? $unit->unit : 'Unit Kerja';
            $penilaian->nama_perusahaan = $provider->nama_perusahaan;
            $penilaian->paket_pekerjaan = $paket->nama_paket;
            $penilaian->nilai_kontrak = $paket->pagu * (0.9 + (rand(0, 9) / 100)); // 90-99% of pagu
            $penilaian->nomor_kontrak = "KONTRAK/" . $paket->nomor;
            $penilaian->tanggal_kontrak = $paket->tanggal_paket;
            $penilaian->alamat_perusahaan = $provider->alamat_perusahaan;
            
            // Tambahkan field wajib lainnya
            $penilaian->lokasi_pekerjaan = $unit ? $unit->unit : 'Rumah Sakit Umum Daerah';
            $penilaian->jangka_waktu = rand(10, 60) . " Hari";
            $penilaian->metode_pemilihan = $paket->metode_pengadaan ?: 'PL';
            $penilaian->pengguna_anggaran = 'dr. SONI, M.Kes'; // Simulasi PA
            
            $ppk = Pegawai::findOne($paket->ppkom);
            $penilaian->pejabat_pembuat_komitmen = $ppk ? $ppk->nama : 'PPK';
            
            // Set created_by berdasarkan siapa yang menilai (kita asumsikan pejabat pengadaan)
            $pejabat = Pegawai::findOne($dpp->pejabat_pengadaan);
            $evaluator_user_id = null;
            
            if ($pejabat && $pejabat->id_user) {
                $evaluator_user_id = $pejabat->id_user;
            } else if ($ppk && $ppk->id_user) {
                $evaluator_user_id = $ppk->id_user;
            }
            
            // Fallback: Jika tidak menemukan id_user, ambil user pertama yang ada di database
            if (!$evaluator_user_id) {
                $firstUser = User::find()->one();
                if ($firstUser) {
                    $evaluator_user_id = $firstUser->id;
                }
            }
            
            if ($evaluator_user_id) {
                $penilaian->created_by = $evaluator_user_id;
                $penilaian->updated_by = $evaluator_user_id;
            }
            
            // Build JSON details
            $details = [
                'uraian' => [],
                'skor' => [],
                'total' => 0,
                'nilaiakhir' => 0,
                'hasil_evaluasi' => '',
                'ulasan_pejabat_pengadaan' => 'ubur2 ikan lele, lanjut leee'
            ];
            
            $total_score = 0;
            foreach ($kriteria_config as $k) {
                // Ambil salah satu skor yang tersedia di template
                $available_scores = $k['scores'];
                $score = $available_scores[array_rand($available_scores)];
                
                $details['uraian'][] = $k['name'];
                $details['skor'][] = (int)$score;
                $total_score += (int)$score;
            }
            
            $count = count($kriteria_config);
            $details['total'] = $total_score;
            $avg = round($total_score / $count, 1);
            
            // Format Nilai Akhir sesuai gambar (A = 4.2)
            $grade = ($avg >= 4) ? "A" : (($avg >= 3) ? "B" : "C");
            $details['nilaiakhir'] = "$grade = $avg";
            
            if ($grade == 'A') $details['hasil_evaluasi'] = 'Direkomendasi untuk digunakan kembali';
            elseif ($grade == 'B') $details['hasil_evaluasi'] = 'Direkomendasi dengan catatan';
            else $details['hasil_evaluasi'] = 'Tidak direkomendasi untuk digunakan kembali';
            
            $penilaian->details = json_encode($details);
            
            // Detach Behaviors agar created_by tidak ter-overwrite (terutama di environment console)
            $penilaian->detachBehavior('blameable');
            $penilaian->detachBehavior('timestamp');
            
            // Set timestamp manual karena behavior di-detach
            $now = date('Y-m-d H:i:s');
            $penilaian->created_at = $now;
            $penilaian->updated_at = $now;
            
            $penilaian->save(false);
        }

        echo "Seeding completed successfully!\n";
        return ExitCode::OK;
    }
}
