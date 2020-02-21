<?php

namespace App\Services\File;

use App\Services\File\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

final class CsvFile implements File
{

    /** @var array **/
    private $headers;
    /** @var string **/
    private $name;
    /** @var string **/
    private $path;
    /** @var array **/
    private $rows;

    /**
     * @param array  $headers
     * @param array  $rows
     * @param string $path
     * @param string $name
     */
    public function __construct(array $headers, array $rows, string $path, string $name)
    {
        $this->headers = $headers;
        $this->name    = $name;
        $this->path    = $path;
        $this->rows    = $rows;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function write(): File
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // headers
        $this->addHeadersTo($this->headers, $sheet);

        // rows
        $this->addRowsTo($this->rows, $sheet);

        // create the .csv file
        $file = fopen($this->path, 'w');

        $writer = (new Csv($spreadsheet))
            ->setDelimiter(';')
            ->setEnclosure('')
            ->setUseBOM(true)
            ->setLineEnding("\r\n")
            ->setSheetIndex(0);

        $writer->save($this->path);

        // close the file
        fclose($file);

        return $this;
    }

    /**
     * Adds the headers to the worksheet
     *
     * @param array     $headers
     * @param Worksheet $sheet
     */
    private function addHeadersTo(array $headers, Worksheet $sheet)
    {
        $column = 1;

        foreach ($headers as $title) {
            $sheet->setCellValueByColumnAndRow($column, 1, $title);

            $column++;
        }
    }

    /**
     * Adds the rows to the worksheet
     *
     * @param array     $rows
     * @param Worksheet $sheet
     */
    private function addRowsTo(array $rows, Worksheet $sheet)
    {
        $rowNb = 2;

        foreach ($rows as $row) {
            $this->addRowTo($rowNb, $row, $sheet);

            $rowNb++;
        }
    }

    /**
     * Adds the row values to the worksheet
     *
     * @param int       $rowNb
     * @param array     $row
     * @param Worksheet $sheet
     */
    private function addRowTo(int $rowNb, array $row, Worksheet $sheet)
    {
        $column = 1;

        foreach ($row as $value) {
            $sheet->setCellValueByColumnAndRow($column, $rowNb, $value);

            $column++;
        }
    }

}
