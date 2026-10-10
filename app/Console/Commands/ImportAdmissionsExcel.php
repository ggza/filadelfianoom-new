<?php

namespace App\Console\Commands;

use App\Models\Admission;
use Carbon\Carbon;
use Illuminate\Console\Command;
use RuntimeException;
use ZipArchive;

/**
 * A régi jelentkezési űrlapok (Drupal webform) Excel-exportjának importja az admissions táblába.
 * Személyes adatot nem ír ki: csak darabszámokat. A mezők titkosítva kerülnek az adatbázisba.
 */
class ImportAdmissionsExcel extends Command
{
    protected $signature = 'admissions:import-excel
        {--hu= : a magyar Excel útvonala (alap: ../downloads/Bekoltozes_urlap.xlsx)}
        {--ro= : a román Excel útvonala (alap: ../downloads/Institutionalizare_urlap.xlsx)}
        {--dry-run : csak ellenőriz és számol, nem ír az adatbázisba}';

    protected $description = 'Régi jelentkezések importja Excelből (HU + RO), ismételhető';

    /** Oszlopok: Serial | képviselő | beköltöző | betegségek | fizetési mód | dátum | telefon | e-mail */
    private const HEADERS = [
        'hu' => ['Serial', 'A beköltöző hivatalos képviselőjének adatai (név, CNP, cím)', 'A beköltöző személy adatai (név, CNP, cím)', 'A beköltöző ismert betegségei', 'Fizetési mód', 'Dátum', 'Telefonszám', 'E-mail'],
        'ro' => ['Serial', 'Date reprezentant legal (nume, CNP, adresă)', 'Date beneficiar (nume, CNP, adresă)', 'Boli cunoscute beneficiar', 'Metoda de plată', 'Data', 'Telefon', 'E-mail'],
    ];

    public function handle(): int
    {
        $files = [
            'hu' => $this->option('hu') ?: base_path('../downloads/Bekoltozes_urlap.xlsx'),
            'ro' => $this->option('ro') ?: base_path('../downloads/Institutionalizare_urlap.xlsx'),
        ];
        $dry = (bool) $this->option('dry-run');
        $total = ['new' => 0, 'skipped_existing' => 0, 'skipped_empty' => 0];

        foreach ($files as $locale => $path) {
            try {
                $rows = $this->readSheet($path);
            } catch (RuntimeException $e) {
                $this->error("[$locale] " . $e->getMessage());
                return self::FAILURE;
            }

            // 2. sor: fejléc (az 1. sor cím). A fejléceknek egyezniük kell az űrlap mezőcímkéivel.
            $head = array_map(fn ($i) => trim((string) ($rows[2][$i] ?? '')), range(0, 7));
            $wrong = array_keys(array_filter(self::HEADERS[$locale], fn ($h, $i) => $h !== $head[$i], ARRAY_FILTER_USE_BOTH));
            if ($wrong) {
                $this->error("[$locale] A fejléc nem egyezik az elvárttal ezekben az oszlopokban: " . implode(', ', array_map(fn ($i) => $i + 1, $wrong)));
                return self::FAILURE;
            }

            $n = ['new' => 0, 'skipped_existing' => 0, 'skipped_empty' => 0, 'numeric_phone' => 0, 'no_date' => 0];
            foreach ($rows as $r => $row) {
                if ($r < 3) continue;
                $serial = (int) ($row[0] ?? 0);
                $rep = $this->text($row[1] ?? null);
                $res = $this->text($row[2] ?? null);
                if (! $serial || $rep === '' || $res === '') { $n['skipped_empty']++; continue; }

                if (Admission::where(['source' => 'excel', 'locale' => $locale, 'legacy_serial' => $serial])->exists()) {
                    $n['skipped_existing']++; continue;
                }

                $phone = $this->text($row[6] ?? null);
                if ($phone !== '' && preg_match('/^\d+$/', $phone)) $n['numeric_phone']++;
                $date = $this->date($row[5] ?? null);
                if (! $date) $n['no_date']++;

                if (! $dry) {
                    Admission::create([
                        'locale' => $locale, 'source' => 'excel', 'legacy_serial' => $serial, 'status' => 'closed',
                        'representative' => $rep, 'resident' => $res,
                        'diseases' => $this->text($row[3] ?? null) ?: null,
                        'payment_method' => $this->text($row[4] ?? null) ?: null,
                        'preferred_date' => $date,
                        'phone' => $phone ?: null,
                        'email' => $this->text($row[7] ?? null) ?: null,
                    ]);
                }
                $n['new']++;
            }

            $this->line(strtoupper($locale) . ': ' . ($dry ? 'importálható ' : 'importálva ') . $n['new']
                . ", már megvolt {$n['skipped_existing']}, üres/hibás sor kihagyva {$n['skipped_empty']}"
                . ", csak számjegyes telefon {$n['numeric_phone']}, dátum nélkül {$n['no_date']}");
            foreach (['new', 'skipped_existing', 'skipped_empty'] as $k) $total[$k] += $n[$k];
        }

        $this->info(($dry ? 'PRÓBAFUTÁS – ' : '') . "Összesen: új {$total['new']}, már megvolt {$total['skipped_existing']}, kihagyva {$total['skipped_empty']}.");
        return self::SUCCESS;
    }

    private function text(mixed $v): string
    {
        if ($v === null) return '';
        $s = trim((string) $v);
        // az Excel tudományos alakja (pl. 4.0745092984E10) → egész szám
        if (preg_match('/^\d+(\.\d+)?[eE]\+?\d+$/', $s)) $s = sprintf('%.0f', (float) $s);
        return $s;
    }

    private function date(mixed $v): ?string
    {
        $s = $this->text($v);
        if ($s === '') return null;
        try {
            if (is_numeric($s)) return Carbon::create(1899, 12, 30)->addDays((int) $s)->toDateString();
            return Carbon::parse($s)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    /** Az első munkalap soronként: [sorszám => [oszlopindex (0-tól) => érték]] (ZipArchive + SimpleXML, külső csomag nélkül). */
    private function readSheet(string $path): array
    {
        if (! is_file($path)) throw new RuntimeException('Nincs ilyen fájl: ' . $path);
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) throw new RuntimeException('Nem nyitható meg: ' . $path);

        $shared = [];
        if (($xml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            foreach (simplexml_load_string($xml)->si as $si) {
                $t = isset($si->t) ? (string) $si->t : '';
                foreach ($si->r ?? [] as $run) $t .= (string) $run->t;
                $shared[] = $t;
            }
        }
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheet === false) throw new RuntimeException('Nincs munkalap a fájlban: ' . $path);

        $rows = [];
        foreach (simplexml_load_string($sheet)->sheetData->row as $row) {
            $r = (int) $row['r'];
            foreach ($row->c as $c) {
                $letters = preg_replace('/\d+/', '', (string) $c['r']);
                $col = 0;
                foreach (str_split($letters) as $ch) $col = $col * 26 + (ord($ch) - 64);
                $type = (string) $c['t'];
                if ($type === 's') $val = $shared[(int) $c->v] ?? '';
                elseif ($type === 'inlineStr') { $val = isset($c->is->t) ? (string) $c->is->t : ''; foreach ($c->is->r ?? [] as $run) $val .= (string) $run->t; }
                else $val = isset($c->v) ? (string) $c->v : null;
                $rows[$r][$col - 1] = $val;
            }
        }

        return $rows;
    }
}
