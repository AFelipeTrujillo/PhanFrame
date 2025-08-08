<?php

namespace PhanFrame;

use InvalidArgumentException;

class CsvReader
{
    /**
     * Reads a CSV file and converts it to a PhanFrame
     * 
     * @param string $filePath Path to the CSV file
     * @param array $options Configuration options
     * @return PhanFrame
     */
    public static function read(string $filePath, array $options = []): PhanFrame
    {
        // Validate that the file exists
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File not found: $filePath");
        }

        // Default options
        $delimiter = $options['delimiter'] ?? self::detectDelimiter($filePath);
        $enclosure = $options['enclosure'] ?? '"';
        $escape = $options['escape'] ?? '\\';
        $hasHeaders = $options['headers'] ?? true;
        $encoding = $options['encoding'] ?? 'UTF-8';

        // Open file
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new InvalidArgumentException("Cannot open file: $filePath");
        }

        $data = [];
        $headers = [];
        $rowIndex = 0;

        while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
            // First row: headers or data
            if ($rowIndex === 0) {
                if ($hasHeaders) {
                    $headers = $row;
                    $rowIndex++;
                    continue;
                } else {
                    // Generate automatic headers
                    $headers = array_map(function($index) {
                        return "column_$index";
                    }, array_keys($row));
                }
            }

            // Process data
            foreach ($row as $colIndex => $value) {
                $columnName = $headers[$colIndex] ?? "column_$colIndex";
                
                // Convert data types
                $data[$columnName][] = self::convertType($value);
            }
            
            $rowIndex++;
        }

        fclose($handle);

        if (empty($data)) {
            throw new InvalidArgumentException("CSV file is empty or invalid: $filePath");
        }

        return new DataFrame($data);
    }

    /**
     * Automatically detects the CSV delimiter
     */
    private static function detectDelimiter(string $filePath): string
    {
        $delimiters = [',', ';', '\t', '|'];
        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            return ','; // Fallback
        }

        $firstLine = fgets($handle);
        fclose($handle);

        $maxCount = 0;
        $detectedDelimiter = ',';

        foreach ($delimiters as $delimiter) {
            $count = substr_count($firstLine, $delimiter);
            if ($count > $maxCount) {
                $maxCount = $count;
                $detectedDelimiter = $delimiter;
            }
        }

        return $detectedDelimiter;
    }

    /**
     * Converts values to their appropriate types
     */
    private static function convertType($value)
    {
        // Null or empty value
        if ($value === '' || $value === null || strtolower($value) === 'null') {
            return null;
        }

        // Booleans
        if (strtolower($value) === 'true') {
            return true;
        }
        if (strtolower($value) === 'false') {
            return false;
        }

        // Integer numbers
        if (is_numeric($value) && strpos($value, '.') === false) {
            return (int) $value;
        }

        // Decimal numbers
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Default string
        return (string) $value;
    }
} 