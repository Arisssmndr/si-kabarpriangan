<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use RuntimeException;
use ZipArchive;

/**
 * Native OOXML (.xlsx) workbook builder for Kasir recapitulation exports.
 *
 * Outputs a valid ZIP-based Excel 2007+ package without relying on fake-XLS HTML
 * tables, preventing Excel's "file format and extension don't match" security alert.
 */
class RekapKasXlsxExporter
{
    private const STYLE_TEXT = 0;
    private const STYLE_TEXT_CENTER = 1;
    private const STYLE_NUM = 2;
    private const STYLE_TITLE = 3;
    private const STYLE_SUBTITLE = 4;
    private const STYLE_BANNER_KI = 5;
    private const STYLE_BANNER_KO = 6;
    private const STYLE_BANNER_SUMMARY = 7;
    private const STYLE_TH_LEFT = 8;
    private const STYLE_TH_CENTER = 9;
    private const STYLE_TH_RIGHT = 10;
    private const STYLE_SUBSECTION_LEFT = 11;
    private const STYLE_SUBSECTION_RIGHT = 12;
    private const STYLE_TOTAL_KI_LABEL = 13;
    private const STYLE_TOTAL_KI_NUM = 14;
    private const STYLE_TOTAL_KI_EMPTY = 15;
    private const STYLE_TOTAL_KO_LABEL = 16;
    private const STYLE_TOTAL_KO_NUM = 17;
    private const STYLE_TOTAL_KO_EMPTY = 18;
    private const STYLE_BALANCE_LABEL = 19;
    private const STYLE_BALANCE_NUM = 20;
    private const STYLE_BALANCE_STATUS = 21;
    private const STYLE_TEXT_BOLD = 22;
    private const STYLE_NUM_BOLD = 23;

    /** @var array<int, string> */
    private array $sharedStrings = [];

    /** @var array<string, int> */
    private array $sharedStringIndex = [];

    /** @var array<int, array<int, array{type: string, value: mixed, style: int}>> */
    private array $rows = [];

    /** @var array<int, string> */
    private array $merges = [];

    /**
     * Build the binary contents of the Rekapitulasi Kas .xlsx workbook.
     */
    public function build(
        Collection $kasMasuks,
        Collection $kasKeluars,
        string $unitLabel,
        string $labelPeriode
    ): string {
        $this->sharedStrings = [];
        $this->sharedStringIndex = [];
        $this->rows = [];
        $this->merges = [];

        $unitUpper = mb_strtoupper($unitLabel, 'UTF-8');

        // Title block
        $this->addMergedBanner(
            "HU KABAR PRIANGAN - {$unitUpper} (REKAPITULASI TRANSAKSI KAS)",
            self::STYLE_TITLE
        );
        $this->addMergedBanner(
            "Unit Pelaksana: {$unitLabel} | {$labelPeriode} | Dicetak: " . Carbon::now()->format('d/m/Y H:i'),
            self::STYLE_SUBTITLE
        );
        $this->addBlankRow();

        // Section 1: Kas Masuk (KI)
        $this->addMergedBanner(
            'RINCIAN KAS MASUK (KI - ' . $kasMasuks->count() . ' Transaksi)',
            self::STYLE_BANNER_KI
        );
        $this->addHeaderRow([
            ['No', self::STYLE_TH_CENTER],
            ['No. Kuitansi', self::STYLE_TH_LEFT],
            ['Tanggal', self::STYLE_TH_CENTER],
            ['Divisi / Bidang', self::STYLE_TH_LEFT],
            ['Kode COA', self::STYLE_TH_LEFT],
            ['Nama Perkiraan', self::STYLE_TH_LEFT],
            ['Terima Dari / Pemasang', self::STYLE_TH_LEFT],
            ['Jumlah (Rp)', self::STYLE_TH_RIGHT],
            ['Status', self::STYLE_TH_CENTER],
        ]);

        $totalKi = 0.0;
        $kiIklan = 0.0;
        $kiSirkulasi = 0.0;
        $kiKeuanganLain = 0.0;

        foreach ($kasMasuks as $idx => $ki) {
            $nominal = (float) ($ki->jumlah_dibayar ?? $ki->jumlah_diterima ?? 0);
            $totalKi += $nominal;

            if ($ki->kode_bagian === '01') {
                $kiIklan += $nominal;
            } elseif ($ki->kode_bagian === '02') {
                $kiSirkulasi += $nominal;
            } else {
                $kiKeuanganLain += $nominal;
            }

            $tgl = $ki->tanggal_terima ? Carbon::parse($ki->tanggal_terima)->format('d/m/Y') : '-';
            $noKuitansi = (string) ($ki->no_kuitansi ?? $ki->no_bkm ?? '-');
            $divisi = (string) ($ki->nama_divisi ?? 'Bidang Keuangan');
            $coa = (string) ($ki->kode_perkiraan ?? '-');
            $namaCoa = (string) ($ki->nama_perkiraan ?? '-');
            $pembayar = (string) ($ki->terima_dari ?? $ki->nama_pembayar ?? '-');
            $status = strtoupper((string) ($ki->status_bayar ?? 'Lunas'));

            $this->rows[] = [
                $this->numCell($idx + 1, self::STYLE_TEXT_CENTER),
                $this->strCell($noKuitansi, self::STYLE_TEXT),
                $this->strCell($tgl, self::STYLE_TEXT_CENTER),
                $this->strCell($divisi, self::STYLE_TEXT),
                $this->strCell($coa, self::STYLE_TEXT),
                $this->strCell($namaCoa, self::STYLE_TEXT),
                $this->strCell($pembayar, self::STYLE_TEXT),
                $this->numCell($nominal, self::STYLE_NUM),
                $this->strCell($status, self::STYLE_TEXT_CENTER),
            ];
        }

        $this->addTotalRow(
            'TOTAL KAS MASUK (KI)',
            $totalKi,
            '',
            self::STYLE_TOTAL_KI_LABEL,
            self::STYLE_TOTAL_KI_NUM,
            self::STYLE_TOTAL_KI_EMPTY
        );
        $this->addBlankRow();

        // Section 2: Kas Keluar (KO)
        $this->addMergedBanner(
            'RINCIAN KAS KELUAR (KO - ' . $kasKeluars->count() . ' Transaksi)',
            self::STYLE_BANNER_KO
        );
        $this->addHeaderRow([
            ['No', self::STYLE_TH_CENTER],
            ['No. BKK (KO)', self::STYLE_TH_LEFT],
            ['Tanggal', self::STYLE_TH_CENTER],
            ['Divisi / Bidang', self::STYLE_TH_LEFT],
            ['No. Register BP', self::STYLE_TH_LEFT],
            ['Kode COA', self::STYLE_TH_LEFT],
            ['Diberikan Kepada / Uraian', self::STYLE_TH_LEFT],
            ['Jumlah (Rp)', self::STYLE_TH_RIGHT],
            ['Kategori', self::STYLE_TH_LEFT],
        ]);

        $totalKo = 0.0;
        $koIklan = 0.0;
        $koSirkulasi = 0.0;
        $koUmum = 0.0;
        $koRedaksi = 0.0;
        $koKeuangan = 0.0;
        $koSetorBank = 0.0;

        foreach ($kasKeluars as $idx => $ko) {
            $nominal = (float) ($ko->nominal_keluar ?? $ko->jumlah_keluar ?? 0);
            $totalKo += $nominal;

            if ($ko->kategori_beban === 'setor_bank') {
                $koSetorBank += $nominal;
            }
            if ($ko->kode_bagian === '01') {
                $koIklan += $nominal;
            } elseif ($ko->kode_bagian === '02') {
                $koSirkulasi += $nominal;
            } elseif ($ko->kode_bagian === '03') {
                $koUmum += $nominal;
            } elseif ($ko->kode_bagian === '06') {
                $koRedaksi += $nominal;
            } else {
                $koKeuangan += $nominal;
            }

            $tgl = $ko->tanggal_keluar ? Carbon::parse($ko->tanggal_keluar)->format('d/m/Y') : '-';
            $noBkk = (string) ($ko->no_bkk ?? '-');
            $divisi = (string) ($ko->nama_divisi ?? 'Bidang Keuangan');
            $reg = (string) ($ko->no_register_anggaran ?? $ko->no_bp_ref ?? '-');
            $coa = (string) ($ko->kode_perkiraan ?? '-');
            $penerima = (string) ($ko->diberikan_kepada ?? $ko->nama_penerima ?? $ko->keperluan ?? '-');
            $kat = (string) ($ko->kategori_beban ?? $ko->kategori ?? 'Operasional');

            $this->rows[] = [
                $this->numCell($idx + 1, self::STYLE_TEXT_CENTER),
                $this->strCell($noBkk, self::STYLE_TEXT),
                $this->strCell($tgl, self::STYLE_TEXT_CENTER),
                $this->strCell($divisi, self::STYLE_TEXT),
                $this->strCell($reg, self::STYLE_TEXT),
                $this->strCell($coa, self::STYLE_TEXT),
                $this->strCell($penerima, self::STYLE_TEXT),
                $this->numCell($nominal, self::STYLE_NUM),
                $this->strCell($kat, self::STYLE_TEXT),
            ];
        }

        $this->addTotalRow(
            'TOTAL KAS KELUAR (KO)',
            $totalKo,
            '',
            self::STYLE_TOTAL_KO_LABEL,
            self::STYLE_TOTAL_KO_NUM,
            self::STYLE_TOTAL_KO_EMPTY
        );
        $this->addBlankRow();

        // Section 3: Rangkuman Subtotal & Keseimbangan Saldo
        $this->addMergedBanner(
            "RANGKUMAN AKUMULASI SUBTOTAL PER DIVISI & KESEIMBANGAN SALDO ({$unitUpper})",
            self::STYLE_BANNER_SUMMARY
        );
        $this->addSummaryHeaderRow('A. AKUMULASI SUBTOTAL PENERIMAAN PER DIVISI (KI)', 'NOMINAL (RP)', 'KETERANGAN');
        $this->addSummaryItemRow('1. Subtotal Penerimaan Divisi Iklan (KI-01)', $kiIklan, 'Pelunasan Faktur & Tunai Iklan');
        $this->addSummaryItemRow('2. Subtotal Penerimaan Divisi Sirkulasi (KI-02)', $kiSirkulasi, 'Setoran Koran Agen');
        $this->addSummaryItemRow('3. Subtotal Penerimaan Bidang Keuangan / Lain-lain (KI-09 & KI-14)', $kiKeuanganLain, 'Koran Sisa, Eceran & Kas Bank');
        $this->addTotalRow(
            'TOTAL PENERIMAAN KONSOLIDASI (KI)',
            $totalKi,
            '',
            self::STYLE_TOTAL_KI_LABEL,
            self::STYLE_TOTAL_KI_NUM,
            self::STYLE_TOTAL_KI_EMPTY
        );

        $this->addSummaryHeaderRow('B. AKUMULASI SUBTOTAL PENGELUARAN PER DIVISI (KO)', 'NOMINAL (RP)', 'KETERANGAN');
        $this->addSummaryItemRow('1. Subtotal Pengeluaran Divisi Iklan (Komisi KH, Insentif AE & BP)', $koIklan, 'Beban Divisi Iklan');
        $this->addSummaryItemRow('2. Subtotal Pengeluaran Divisi Sirkulasi', $koSirkulasi, 'Beban Distribusi & Sirkulasi');
        $this->addSummaryItemRow('3. Subtotal Pengeluaran Divisi Umum & SDM', $koUmum, 'Beban Operasional Umum');
        $this->addSummaryItemRow('4. Subtotal Pengeluaran Divisi Redaksi', $koRedaksi, 'Beban Liputan & Redaksi');
        $this->addSummaryItemRow('5. Subtotal Pengeluaran Bidang Keuangan (Termasuk Setor Kas ke Bank)', $koKeuangan, 'Bidang Keuangan & Banking');
        $this->addTotalRow(
            'TOTAL PENGELUARAN KONSOLIDASI (KO)',
            $totalKo,
            '',
            self::STYLE_TOTAL_KO_LABEL,
            self::STYLE_TOTAL_KO_NUM,
            self::STYLE_TOTAL_KO_EMPTY
        );

        $saldoBersihKasFisik = $totalKi - $totalKo;
        $totalSaldoKasDanBankSeimbang = $saldoBersihKasFisik + $koSetorBank;

        $this->addSummaryHeaderRow('C. REKONSILIASI SALDO AKHIR KAS FISIK & BANK (BALANCE)', 'NOMINAL (RP)', 'STATUS');
        $this->addSummaryItemRow('Saldo Akhir Kas Fisik Brankas (Total KI - Total KO)', $saldoBersihKasFisik, 'Kas Fisik', true);
        $this->addSummaryItemRow('Akumulasi Setoran Kas ke Rekening Bank (KO-09 Setor Bank)', $koSetorBank, 'Mutasi Bank', true);
        $this->addTotalRow(
            'TOTAL SALDO AKHIR KAS FISIK & BANK SEIMBANG (BALANCE = KI - BEBAN OPERASIONAL)',
            $totalSaldoKasDanBankSeimbang,
            'BALANCE (SEIMBANG)',
            self::STYLE_BALANCE_LABEL,
            self::STYLE_BALANCE_NUM,
            self::STYLE_BALANCE_STATUS
        );

        return $this->packageXlsx();
    }

    private function addMergedBanner(string $text, int $style): void
    {
        $rowNum = count($this->rows) + 1;
        $row = [$this->strCell($text, $style)];
        for ($c = 1; $c < 9; $c++) {
            $row[] = $this->emptyCell($style);
        }
        $this->rows[] = $row;
        $this->merges[] = "A{$rowNum}:I{$rowNum}";
    }

    private function addBlankRow(): void
    {
        $this->rows[] = [];
    }

    /**
     * @param array<int, array{0: string, 1: int}> $columns
     */
    private function addHeaderRow(array $columns): void
    {
        $row = [];
        foreach ($columns as [$label, $style]) {
            $row[] = $this->strCell($label, $style);
        }
        $this->rows[] = $row;
    }

    private function addSummaryHeaderRow(string $leftLabel, string $midLabel, string $rightLabel): void
    {
        $rowNum = count($this->rows) + 1;
        $row = [$this->strCell($leftLabel, self::STYLE_SUBSECTION_LEFT)];
        for ($c = 1; $c < 7; $c++) {
            $row[] = $this->emptyCell(self::STYLE_SUBSECTION_LEFT);
        }
        $row[] = $this->strCell($midLabel, self::STYLE_SUBSECTION_RIGHT);
        $row[] = $this->strCell($rightLabel, self::STYLE_SUBSECTION_LEFT);
        $this->rows[] = $row;
        $this->merges[] = "A{$rowNum}:G{$rowNum}";
    }

    private function addSummaryItemRow(string $label, float $amount, string $note, bool $bold = false): void
    {
        $rowNum = count($this->rows) + 1;
        $textStyle = $bold ? self::STYLE_TEXT_BOLD : self::STYLE_TEXT;
        $numStyle = $bold ? self::STYLE_NUM_BOLD : self::STYLE_NUM;

        $row = [$this->strCell($label, $textStyle)];
        for ($c = 1; $c < 7; $c++) {
            $row[] = $this->emptyCell($textStyle);
        }
        $row[] = $this->numCell($amount, $numStyle);
        $row[] = $this->strCell($note, self::STYLE_TEXT);
        $this->rows[] = $row;
        $this->merges[] = "A{$rowNum}:G{$rowNum}";
    }

    private function addTotalRow(
        string $label,
        float $amount,
        string $rightText,
        int $labelStyle,
        int $numStyle,
        int $rightStyle
    ): void {
        $rowNum = count($this->rows) + 1;
        $row = [$this->strCell($label, $labelStyle)];
        for ($c = 1; $c < 7; $c++) {
            $row[] = $this->emptyCell($labelStyle);
        }
        $row[] = $this->numCell($amount, $numStyle);
        $row[] = $rightText !== '' ? $this->strCell($rightText, $rightStyle) : $this->emptyCell($rightStyle);
        $this->rows[] = $row;
        $this->merges[] = "A{$rowNum}:G{$rowNum}";
    }

    /**
     * @return array{type: string, value: int, style: int}
     */
    private function strCell(string $value, int $style): array
    {
        $clean = $this->sanitizeXmlString($value);
        if (!isset($this->sharedStringIndex[$clean])) {
            $this->sharedStringIndex[$clean] = count($this->sharedStrings);
            $this->sharedStrings[] = $clean;
        }

        return [
            'type'  => 's',
            'value' => $this->sharedStringIndex[$clean],
            'style' => $style,
        ];
    }

    /**
     * @return array{type: string, value: float|int, style: int}
     */
    private function numCell(float|int $value, int $style): array
    {
        return [
            'type'  => 'n',
            'value' => $value,
            'style' => $style,
        ];
    }

    /**
     * @return array{type: string, value: null, style: int}
     */
    private function emptyCell(int $style): array
    {
        return [
            'type'  => 'e',
            'value' => null,
            'style' => $style,
        ];
    }

    private function sanitizeXmlString(string $value): string
    {
        return (string) preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $value);
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function packageXlsx(): string
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'kp_xlsx_');
        if ($tmpPath === false) {
            throw new RuntimeException('Gagal membuat berkas sementara untuk export Excel.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($tmpPath);
            throw new RuntimeException('Gagal menginisialisasi arsip XLSX.');
        }

        try {
            $zip->addFromString('[Content_Types].xml', $this->buildContentTypesXml());
            $zip->addFromString('_rels/.rels', $this->buildRootRelsXml());
            $zip->addFromString('xl/workbook.xml', $this->buildWorkbookXml());
            $zip->addFromString('xl/_rels/workbook.xml.rels', $this->buildWorkbookRelsXml());
            $zip->addFromString('xl/styles.xml', $this->buildStylesXml());
            $zip->addFromString('xl/sharedStrings.xml', $this->buildSharedStringsXml());
            $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildSheetXml());
            $zip->close();

            $binary = file_get_contents($tmpPath);
            if ($binary === false) {
                throw new RuntimeException('Gagal membaca berkas XLSX hasil export.');
            }

            return $binary;
        } finally {
            @unlink($tmpPath);
        }
    }

    private function buildContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . '</Types>';
    }

    private function buildRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function buildWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Rekap Kas Keuangan" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function buildWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
            . '</Relationships>';
    }

    private function buildSharedStringsXml(): string
    {
        $count = count($this->sharedStrings);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . $count . '" uniqueCount="' . $count . '">';

        foreach ($this->sharedStrings as $str) {
            $escaped = $this->escapeXml($str);
            $preserve = (preg_match('/^\s|\s$/', $str) === 1) ? ' xml:space="preserve"' : '';
            $xml .= "<si><t{$preserve}>{$escaped}</t></si>";
        }

        return $xml . '</sst>';
    }

    private function buildSheetXml(): string
    {
        $colLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        $colWidths = [6, 18, 13, 24, 16, 26, 36, 18, 22];

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetViews><sheetView workbookViewId="0"/></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="20"/>'
            . '<cols>';

        foreach ($colWidths as $idx => $width) {
            $colNum = $idx + 1;
            $xml .= "<col min=\"{$colNum}\" max=\"{$colNum}\" width=\"{$width}\" customWidth=\"1\"/>";
        }
        $xml .= '</cols><sheetData>';

        foreach ($this->rows as $rIdx => $cells) {
            $rowNum = $rIdx + 1;
            if ($cells === []) {
                $xml .= "<row r=\"{$rowNum}\" ht=\"12\" customHeight=\"1\"/>";
                continue;
            }

            $xml .= "<row r=\"{$rowNum}\">";
            foreach ($cells as $cIdx => $cell) {
                $ref = $colLetters[$cIdx] . $rowNum;
                $s = $cell['style'];
                if ($cell['type'] === 's') {
                    $xml .= "<c r=\"{$ref}\" t=\"s\" s=\"{$s}\"><v>{$cell['value']}</v></c>";
                } elseif ($cell['type'] === 'n') {
                    $numVal = is_int($cell['value'])
                        ? (string) $cell['value']
                        : rtrim(rtrim(number_format((float) $cell['value'], 2, '.', ''), '0'), '.');
                    if ($numVal === '' || $numVal === '-0') {
                        $numVal = '0';
                    }
                    $xml .= "<c r=\"{$ref}\" t=\"n\" s=\"{$s}\"><v>{$numVal}</v></c>";
                } else {
                    $xml .= "<c r=\"{$ref}\" s=\"{$s}\"/>";
                }
            }
            $xml .= '</row>';
        }

        $xml .= '</sheetData>';

        if ($this->merges !== []) {
            $xml .= '<mergeCells count="' . count($this->merges) . '">';
            foreach ($this->merges as $ref) {
                $xml .= "<mergeCell ref=\"{$ref}\"/>";
            }
            $xml .= '</mergeCells>';
        }

        $xml .= '<pageSetup orientation="landscape" paperSize="9"/></worksheet>';

        return $xml;
    }

    private function buildStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="1">'
            . '<numFmt numFmtId="164" formatCode="#,##0"/>'
            . '</numFmts>'
            . '<fonts count="7">'
            . '<font><sz val="10"/><color rgb="FF0F172A"/><name val="Arial"/></font>'
            . '<font><b/><sz val="10"/><color rgb="FF0F172A"/><name val="Arial"/></font>'
            . '<font><b/><sz val="13"/><color rgb="FF0A72AC"/><name val="Arial"/></font>'
            . '<font><sz val="10"/><color rgb="FF475569"/><name val="Arial"/></font>'
            . '<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Arial"/></font>'
            . '<font><b/><sz val="10"/><color rgb="FFBE123C"/><name val="Arial"/></font>'
            . '<font><b/><sz val="11"/><color rgb="FF047857"/><name val="Arial"/></font>'
            . '</fonts>'
            . '<fills count="10">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF0A72AC"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF0E527B"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFBE123C"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF0F766E"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFECFDF5"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFFFF1F2"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFDCFCE7"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="4">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border>'
            . '<left style="thin"><color rgb="FFCBD5E1"/></left>'
            . '<right style="thin"><color rgb="FFCBD5E1"/></right>'
            . '<top style="thin"><color rgb="FFCBD5E1"/></top>'
            . '<bottom style="thin"><color rgb="FFCBD5E1"/></bottom>'
            . '<diagonal/>'
            . '</border>'
            . '<border>'
            . '<left style="thin"><color rgb="FF0A72AC"/></left>'
            . '<right style="thin"><color rgb="FF0A72AC"/></right>'
            . '<top style="thin"><color rgb="FF0A72AC"/></top>'
            . '<bottom style="medium"><color rgb="FFFFB900"/></bottom>'
            . '<diagonal/>'
            . '</border>'
            . '<border>'
            . '<left style="thin"><color rgb="FF94A3B8"/></left>'
            . '<right style="thin"><color rgb="FF94A3B8"/></right>'
            . '<top style="thin"><color rgb="FF94A3B8"/></top>'
            . '<bottom style="thin"><color rgb="FF94A3B8"/></bottom>'
            . '<diagonal/>'
            . '</border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="24">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="4" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="4" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="4" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="4" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="4" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="7" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '<xf numFmtId="164" fontId="1" fillId="7" borderId="3" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="7" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="8" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '<xf numFmtId="164" fontId="5" fillId="8" borderId="3" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="8" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="9" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '<xf numFmtId="164" fontId="6" fillId="9" borderId="3" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="9" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="164" fontId="1" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '<dxfs count="0"/>'
            . '<tableStyles count="0" defaultTableStyle="TableStyleMedium2" defaultPivotStyle="PivotStyleLight16"/>'
            . '</styleSheet>';
    }
}
