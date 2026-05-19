<?php

namespace App\Support;

use RuntimeException;
use SimpleXMLElement;
use XMLReader;
use ZipArchive;

class XlsxWorkbook
{
    private const MAIN_NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    private ZipArchive $zip;
    private array $sheetPaths = [];
    private array $sharedStrings = [];

    public function __construct(private readonly string $path)
    {
        $this->zip = new ZipArchive();
        if ($this->zip->open($this->path) !== true) {
            throw new RuntimeException("Tidak bisa membuka workbook: {$this->path}");
        }

        $this->loadSheetPaths();
        $this->loadSharedStrings();
    }

    public function rows(string $sheetName): iterable
    {
        $path = $this->sheetPaths[$sheetName] ?? null;
        if (! $path) {
            throw new RuntimeException("Sheet tidak ditemukan: {$sheetName}");
        }

        $stream = $this->zip->getStream($path);
        if (! $stream) {
            throw new RuntimeException("Tidak bisa membaca sheet: {$sheetName}");
        }

        $reader = new XMLReader();
        $reader->XML(stream_get_contents($stream));

        $headers = null;
        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT || $reader->name !== 'row') {
                continue;
            }

            $row = $this->readRow($reader);
            if ($headers === null) {
                $headers = $row;
                continue;
            }

            if ($row === []) {
                continue;
            }

            yield array_combine($headers, array_pad($row, count($headers), null));
        }

        $reader->close();
    }

    public function countRows(string $sheetName): int
    {
        $count = 0;
        foreach ($this->rows($sheetName) as $_) {
            $count++;
        }

        return $count;
    }

    private function loadSheetPaths(): void
    {
        $workbookXml = $this->xml('xl/workbook.xml');
        $relsXml = $this->xml('xl/_rels/workbook.xml.rels');

        $rels = [];
        foreach ($relsXml->Relationship as $relationship) {
            $attributes = $relationship->attributes();
            $rels[(string) $attributes['Id']] = $this->normalizeRelationshipTarget((string) $attributes['Target']);
        }

        foreach ($workbookXml->sheets->sheet as $sheet) {
            $attributes = $sheet->attributes();
            $relAttributes = $sheet->attributes('r', true);
            $this->sheetPaths[(string) $attributes['name']] = $rels[(string) $relAttributes['id']] ?? null;
        }
    }

    private function loadSharedStrings(): void
    {
        $xml = $this->zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return;
        }

        $reader = new XMLReader();
        $reader->XML($xml);
        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->name === 'si') {
                $node = new SimpleXMLElement($reader->readOuterXML());
                $this->sharedStrings[] = $this->flattenText($node);
            }
        }
        $reader->close();
    }

    private function normalizeRelationshipTarget(string $target): string
    {
        $target = str_replace('\\', '/', $target);

        if (str_starts_with($target, '/')) {
            return ltrim($target, '/');
        }

        return 'xl/'.ltrim($target, '/');
    }

    private function readRow(XMLReader $reader): array
    {
        $rowXml = $reader->readOuterXML();
        $row = new SimpleXMLElement($rowXml);
        $values = [];

        $row->registerXPathNamespace('m', self::MAIN_NS);

        foreach ($row->xpath('m:c') ?: [] as $cell) {
            $attributes = $cell->attributes();
            $reference = (string) ($attributes['r'] ?? '');
            $index = $this->columnIndex($reference);
            $values[$index] = $this->cellValue($cell);
        }

        if ($values === []) {
            return [];
        }

        $max = max(array_keys($values));
        $normalized = [];
        for ($i = 0; $i <= $max; $i++) {
            $normalized[] = $values[$i] ?? null;
        }

        return $normalized;
    }

    private function cellValue(SimpleXMLElement $cell): mixed
    {
        $attributes = $cell->attributes();
        $type = (string) ($attributes['t'] ?? '');

        if ($type === 's') {
            $index = (int) $cell->v;
            return $this->sharedStrings[$index] ?? null;
        }

        if ($type === 'inlineStr') {
            return $this->flattenText($cell);
        }

        $cell->registerXPathNamespace('m', self::MAIN_NS);
        $values = $cell->xpath('m:v');
        $value = $values ? (string) $values[0] : null;
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? $value + 0 : $value;
    }

    private function columnIndex(string $reference): int
    {
        preg_match('/^[A-Z]+/', $reference, $matches);
        $letters = $matches[0] ?? 'A';
        $index = 0;
        foreach (str_split($letters) as $letter) {
            $index = $index * 26 + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private function flattenText(SimpleXMLElement $node): string
    {
        $node->registerXPathNamespace('m', self::MAIN_NS);
        $parts = [];
        foreach ($node->xpath('.//m:t') ?: [] as $text) {
            $parts[] = (string) $text;
        }

        return implode('', $parts);
    }

    private function xml(string $path): SimpleXMLElement
    {
        $xml = $this->zip->getFromName($path);
        if ($xml === false) {
            throw new RuntimeException("File XLSX tidak lengkap: {$path}");
        }

        return new SimpleXMLElement($xml);
    }
}
