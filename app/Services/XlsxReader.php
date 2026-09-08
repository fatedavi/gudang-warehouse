<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use RuntimeException;
use ZipArchive;

class XlsxReader
{
    public static function rows(string $path): array
    {
        if (! file_exists($path)) {
            throw new RuntimeException("File xlsx tidak ditemukan: {$path}");
        }

        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new RuntimeException("Tidak dapat membuka file xlsx: {$path}");
        }

        $strings = self::sharedStrings($zip->getFromName('xl/sharedStrings.xml'));
        $grid = self::grid($zip->getFromName('xl/worksheets/sheet1.xml'), $strings);
        $zip->close();

        $headerRow = self::detectHeaderRow($grid);

        $rows = [];
        foreach ($grid as $nomorBaris => $cells) {
            if ($nomorBaris === $headerRow || $cells === []) {
                continue;
            }

            $row = [];
            foreach ($cells as $kolom => $nilai) {
                $kunci = strtolower(str_replace(' ', '_', trim($grid[$headerRow][$kolom] ?? '')));
                if ($kunci === '') {
                    continue;
                }
                $row[$kunci] = $nilai;
            }

            if ($row !== []) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private static function detectHeaderRow(array $grid): int
    {
        foreach ($grid as $nomorBaris => $cells) {
            foreach ($cells as $nilai) {
                if (strtolower(trim((string) $nilai)) === 'kode_produk') {
                    return $nomorBaris;
                }
            }
        }

        foreach ($grid as $nomorBaris => $cells) {
            if (count($cells) >= 5) {
                return $nomorBaris;
            }
        }

        throw new RuntimeException('Struktur header xlsx tidak ditemukan.');
    }

    private static function grid(string $xml, array $strings): array
    {
        $grid = [];

        if ($xml === false || $xml === '') {
            return $grid;
        }

        $doc = new DOMDocument;
        $doc->loadXML($xml);
        $xp = new DOMXPath($doc);
        $xp->registerNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        foreach ($xp->query('//a:sheetData/a:row') as $row) {
            $nomor = (int) $row->getAttribute('r');
            foreach ($xp->query('a:c', $row) as $cell) {
                $kolom = preg_replace('/[0-9]/', '', $cell->getAttribute('r'));
                $tipe = $cell->getAttribute('t');
                $v = $cell->getElementsByTagName('v')->item(0);
                $nilai = $v ? $v->textContent : '';

                if ($tipe === 's' && $nilai !== '') {
                    $nilai = $strings[(int) $nilai] ?? '';
                }

                $grid[$nomor][$kolom] = $nilai;
            }
        }

        return $grid;
    }

    private static function sharedStrings(string|false $xml): array
    {
        $strings = [];

        if ($xml === false || $xml === '') {
            return $strings;
        }

        $doc = new DOMDocument;
        $doc->loadXML($xml);
        $xp = new DOMXPath($doc);
        $xp->registerNamespace('a', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        foreach ($xp->query('//a:si') as $si) {
            $strings[] = $si->textContent;
        }

        return $strings;
    }
}
