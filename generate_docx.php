<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Table as TableStyle;

$mdPath = __DIR__ . '/PANDUAN_OPERASIONAL_SISTEM_KABAR_PRIANGAN.md';
$outputPath1 = __DIR__ . '/PANDUAN_OPERASIONAL_SISTEM_KABAR_PRIANGAN.docx';
$outputPath2 = __DIR__ . '/PANDUAN_LENGKAP_OPERASIONAL_KASIR_KABAR_PRIANGAN.docx';

if (!file_exists($mdPath)) {
    die("File markdown tidak ditemukan: {$mdPath}\n");
}

$lines = file($mdPath, FILE_IGNORE_NEW_LINES);

$phpWord = new PhpWord();

// Default document settings
$phpWord->setDefaultFontName('Calibri');
$phpWord->setDefaultFontSize(11);

// Styles
$titleFont = ['name' => 'Calibri', 'size' => 20, 'bold' => true, 'color' => '0F3E6D'];
$subtitleFont = ['name' => 'Calibri', 'size' => 13, 'bold' => true, 'color' => '047857'];
$metaFont = ['name' => 'Calibri', 'size' => 10, 'italic' => true, 'color' => '475569'];

$h1Font = ['name' => 'Calibri', 'size' => 15, 'bold' => true, 'color' => '0F3E6D'];
$h1Para = ['spaceBefore' => 240, 'spaceAfter' => 120, 'keepNext' => true];

$h2Font = ['name' => 'Calibri', 'size' => 12.5, 'bold' => true, 'color' => '047857'];
$h2Para = ['spaceBefore' => 180, 'spaceAfter' => 80, 'keepNext' => true];

$h3Font = ['name' => 'Calibri', 'size' => 11.5, 'bold' => true, 'color' => '1E293B'];
$h3Para = ['spaceBefore' => 140, 'spaceAfter' => 60, 'keepNext' => true];

$bodyFont = ['name' => 'Calibri', 'size' => 10.5, 'color' => '1E293B'];
$bodyPara = ['spaceBefore' => 0, 'spaceAfter' => 80, 'lineHeight' => 1.15];

$bulletFont = ['name' => 'Calibri', 'size' => 10.5, 'color' => '1E293B'];
$bulletPara = ['spaceBefore' => 0, 'spaceAfter' => 40, 'indentation' => ['left' => 360]];

$codeFont = ['name' => 'Consolas', 'size' => 9, 'color' => '0F172A'];
$codePara = ['spaceBefore' => 20, 'spaceAfter' => 20, 'indentation' => ['left' => 360]];

// Table styles
$tableStyleName = 'KabarPrianganTable';
$phpWord->addTableStyle($tableStyleName, [
    'borderSize'  => 6,
    'borderColor' => 'CBD5E1',
    'cellMarginTop' => 100,
    'cellMarginBottom' => 100,
    'cellMarginLeft' => 150,
    'cellMarginRight' => 150,
    'alignment' => JcTable::CENTER,
]);

$thStyle = ['bgColor' => '0F3E6D'];
$thFont = ['name' => 'Calibri', 'size' => 9.5, 'bold' => true, 'color' => 'FFFFFF'];
$tdFont = ['name' => 'Calibri', 'size' => 9, 'color' => '1E293B'];
$tdAltStyle = ['bgColor' => 'F8FAFC'];

// Add Section with standard A4 margins
$section = $phpWord->addSection([
    'paperSize' => 'A4',
    'marginTop' => 1200,
    'marginBottom' => 1200,
    'marginLeft' => 1200,
    'marginRight' => 1200,
]);

// Header & Footer
$header = $section->addHeader();
$header->addText(
    'Harian Umum Kabar Priangan — Buku Panduan Resmi Operasional Kasir',
    ['name' => 'Calibri', 'size' => 8.5, 'italic' => true, 'color' => '64748B'],
    ['alignment' => Jc::RIGHT]
);

$footer = $section->addFooter();
$footer->addPreserveText(
    'Halaman {PAGE} dari {NUMPAGES}',
    ['name' => 'Calibri', 'size' => 8.5, 'color' => '64748B'],
    ['alignment' => Jc::CENTER]
);

$i = 0;
$totalLines = count($lines);
$inCodeBlock = false;
$codeLines = [];
$inTable = false;
$tableRows = [];

while ($i < $totalLines) {
    $line = rtrim($lines[$i]);

    // Handle Code Block
    if (str_starts_with($line, '```')) {
        if ($inCodeBlock) {
            // End of code block
            $inCodeBlock = false;
            // Render code block inside a table with 1 cell for box styling
            $boxTable = $section->addTable(['borderSize' => 6, 'borderColor' => 'CBD5E1', 'cellMargin' => 120]);
            $boxTable->addRow();
            $cell = $boxTable->addCell(9000, ['bgColor' => 'F1F5F9']);
            foreach ($codeLines as $cl) {
                $cell->addText(htmlspecialchars($cl, ENT_COMPAT, 'UTF-8'), $codeFont, $codePara);
            }
            $section->addTextBreak(1);
            $codeLines = [];
        } else {
            $inCodeBlock = true;
            $codeLines = [];
        }
        $i++;
        continue;
    }

    if ($inCodeBlock) {
        $codeLines[] = $line;
        $i++;
        continue;
    }

    // Handle Markdown Tables
    if (str_starts_with(trim($line), '|')) {
        $tableRows[] = $line;
        $i++;
        continue;
    } else {
        if (!empty($tableRows)) {
            // Process accumulated table
            renderMarkdownTable($section, $tableRows, $tableStyleName, $thStyle, $thFont, $tdFont, $tdAltStyle);
            $tableRows = [];
            $section->addTextBreak(1);
        }
    }

    // Empty lines
    if (trim($line) === '') {
        $i++;
        continue;
    }

    // Horizontal Rule
    if (trim($line) === '---') {
        $section->addTextBreak(1);
        $i++;
        continue;
    }

    // Headings
    if (str_starts_with($line, '# ')) {
        $text = htmlspecialchars(trim(substr($line, 2)), ENT_COMPAT, 'UTF-8');
        $section->addText($text, $titleFont, ['spaceBefore' => 100, 'spaceAfter' => 60]);
    } elseif (str_starts_with($line, '## ')) {
        $text = htmlspecialchars(trim(substr($line, 3)), ENT_COMPAT, 'UTF-8');
        if (str_contains($text, 'Sistem Informasi Manajemen')) {
            $section->addText($text, $subtitleFont, ['spaceBefore' => 40, 'spaceAfter' => 80]);
        } else {
            $section->addText($text, $h1Font, $h1Para);
        }
    } elseif (str_starts_with($line, '### ')) {
        $text = htmlspecialchars(trim(substr($line, 4)), ENT_COMPAT, 'UTF-8');
        $section->addText($text, $h2Font, $h2Para);
    } elseif (str_starts_with($line, '#### ')) {
        $text = htmlspecialchars(trim(substr($line, 5)), ENT_COMPAT, 'UTF-8');
        $section->addText($text, $h3Font, $h3Para);
    } elseif (str_starts_with(trim($line), '- ') || str_starts_with(trim($line), '* ')) {
        // Bullet points
        $indentCount = (strlen($line) - strlen(ltrim($line)));
        $rawText = ltrim(trim($line), '-* ');
        
        $p = $section->addTextRun([
            'spaceBefore' => 0, 
            'spaceAfter' => 40, 
            'indentation' => ['left' => 360 + ($indentCount * 180)]
        ]);
        $p->addText('• ', ['bold' => true, 'color' => '047857']);
        parseInlineFormatting($p, $rawText, $bulletFont);
    } elseif (preg_match('/^(\d+)\.\s+(.*)$/', trim($line), $matches)) {
        // Numbered list
        $p = $section->addTextRun([
            'spaceBefore' => 0, 
            'spaceAfter' => 40, 
            'indentation' => ['left' => 360]
        ]);
        $p->addText($matches[1] . '. ', ['bold' => true, 'color' => '0F3E6D']);
        parseInlineFormatting($p, $matches[2], $bulletFont);
    } else {
        // Normal paragraph
        $p = $section->addTextRun($bodyPara);
        parseInlineFormatting($p, $line, $bodyFont);
    }

    $i++;
}

// If file ends with table
if (!empty($tableRows)) {
    renderMarkdownTable($section, $tableRows, $tableStyleName, $thStyle, $thFont, $tdFont, $tdAltStyle);
}

// Helper to render markdown table into Word
function renderMarkdownTable($section, array $rows, $styleName, $thStyle, $thFont, $tdFont, $tdAltStyle)
{
    $cleanRows = [];
    foreach ($rows as $r) {
        $trimmed = trim($r);
        // Skip separator row like |:---|:---|
        if (preg_match('/^\|(\s*[:\-]+\s*\|)+$/', $trimmed)) {
            continue;
        }
        $cols = explode('|', $trimmed);
        // remove first and last empty element if line starts/ends with |
        if (trim($cols[0]) === '') array_shift($cols);
        if (!empty($cols) && trim($cols[count($cols) - 1]) === '') array_pop($cols);
        $cleanRows[] = array_map('trim', $cols);
    }

    if (empty($cleanRows)) return;

    $colCount = count($cleanRows[0]);
    // Determine cell widths based on col count
    $totalWidth = 9200;
    $colWidth = (int) ($totalWidth / max(1, $colCount));

    $table = $section->addTable($styleName);

    foreach ($cleanRows as $rowIndex => $cols) {
        $table->addRow();
        $isHeader = ($rowIndex === 0);
        foreach ($cols as $colIndex => $cellContent) {
            $cStyle = $isHeader ? $thStyle : ($rowIndex % 2 === 1 ? $tdAltStyle : []);
            $cFont = $isHeader ? $thFont : $tdFont;

            // Give slightly wider first col if table has 2-4 cols
            $w = $colWidth;
            if ($colCount === 4 && $colIndex === 0) $w = 2000;
            if ($colCount === 4 && $colIndex === 1) $w = 2400;
            if ($colCount === 4 && $colIndex === 2) $w = 1400;
            if ($colCount === 4 && $colIndex === 3) $w = 3400;

            if ($colCount === 3 && $colIndex === 0) $w = 1800;
            if ($colCount === 3 && $colIndex === 1) $w = 2400;
            if ($colCount === 3 && $colIndex === 2) $w = 5000;

            if ($colCount === 2 && $colIndex === 0) $w = 2800;
            if ($colCount === 2 && $colIndex === 1) $w = 6400;

            $cell = $table->addCell($w, $cStyle);
            $p = $cell->addTextRun(['spaceBefore' => 0, 'spaceAfter' => 0]);
            parseInlineFormatting($p, $cellContent, $cFont);
        }
    }
}

// Parse inline formatting (**bold**, *italic*, `code`, etc.)
function parseInlineFormatting($paragraph, string $text, array $baseFont)
{
    // Tokenize markdown bold, italic, code
    $pattern = '/(\*\*.*?\*\*|\*.*?\*|`.*?`)/';
    $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($parts as $part) {
        if ($part === '') continue;

        if (str_starts_with($part, '**') && str_ends_with($part, '**') && strlen($part) >= 4) {
            $clean = htmlspecialchars(substr($part, 2, -2), ENT_COMPAT, 'UTF-8');
            $f = array_merge($baseFont, ['bold' => true]);
            $paragraph->addText($clean, $f);
        } elseif (str_starts_with($part, '*') && str_ends_with($part, '*') && strlen($part) >= 2) {
            $clean = htmlspecialchars(substr($part, 1, -1), ENT_COMPAT, 'UTF-8');
            $f = array_merge($baseFont, ['italic' => true]);
            $paragraph->addText($clean, $f);
        } elseif (str_starts_with($part, '`') && str_ends_with($part, '`') && strlen($part) >= 2) {
            $clean = htmlspecialchars(substr($part, 1, -1), ENT_COMPAT, 'UTF-8');
            $f = array_merge($baseFont, ['name' => 'Consolas', 'size' => 9.5, 'color' => 'B45309']);
            $paragraph->addText($clean, $f);
        } else {
            $clean = htmlspecialchars($part, ENT_COMPAT, 'UTF-8');
            $paragraph->addText($clean, $baseFont);
        }
    }
}

// Save document
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($outputPath1);
copy($outputPath1, $outputPath2);
copy($outputPath1, __DIR__ . '/MODUL_PANDUAN_ADMIN_KASIR_KABAR_PRIANGAN.docx');

// Verifikasi pembacaan ulang file docx
$verifyDoc = \PhpOffice\PhpWord\IOFactory::load($outputPath1);
$sectionCount = count($verifyDoc->getSections());
echo "Integritas Dokumen Terverifikasi: {$sectionCount} section terbaca normal.\n";

echo "BERHASIL DIUPDATE!\n";
echo "1. PANDUAN_OPERASIONAL_SISTEM_KABAR_PRIANGAN.docx\n";
echo "2. PANDUAN_LENGKAP_OPERASIONAL_KASIR_KABAR_PRIANGAN.docx\n";
echo "3. MODUL_PANDUAN_ADMIN_KASIR_KABAR_PRIANGAN.docx\n";
