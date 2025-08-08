<?php

namespace PhanFrame;

use InvalidArgumentException;

class PhanFrame
{
    private $data;
    private $data_type;
    private $groupByColumns = [];

    /**
     * Creates a PhanFrame from a CSV file
     * 
     * @param string $filePath Path to the CSV file
     * @param array $options Configuration options
     * @return PhanFrame
     */
    public static function fromCsv(string $filePath, array $options = []): PhanFrame
    {
        return CsvReader::read($filePath, $options);
    }

    public function __construct($data, $groupByColumns = [])
    {
        $this->data             = $data;
        $this->data_type        = $this->dtype();
        $this->groupByColumns   = $groupByColumns;
    }

    public function __toString(): string
    {
        $output = $this->formatTable($this->head(5)) . PHP_EOL;
        return $output;
    }
    public function head($rows = 5): array
    {
        $result = [];

        foreach ($this->data as $column => $values) {
            $result[$column] = array_slice($values, 0, $rows);
        }

        return $result;
    }

    public function tail($rows = 5): array
    {
        $result = [];

        foreach ($this->data as $column => $values) {
            $result[$column] = array_slice($values, -$rows, $rows);
        }

        return $result;
    }

    public function selectColumns($columns): PhanFrame
    {
        $existingColumns = array_intersect($columns, array_keys($this->data));

        if (empty($existingColumns)) {
            throw new InvalidArgumentException('Columns not found');
        }

        $selectedData = [];

        foreach ($existingColumns as $column) {
            $selectedData[$column] = $this->data[$column];
        }

        return new self($selectedData);
    }

    public function dtype(): array
    {
        $result = [];

        foreach ($this->data as $columnName => $columnValues) {
            $types = array_map('gettype', $columnValues);

            $uniqueTypes = array_unique($types);

            if (count($uniqueTypes) === 1) {
                $result[$columnName] = $uniqueTypes[0];
            } else {
                $result[$columnName] = 'mixed';
            }
        }

        return $result;
    }

    public function mean(): array
    {
        $result = [];

        $dataTypes = $this->data_type ?? $this->dtype();

        foreach ($this->data as $columnName => $columnValues) {
            if (in_array($dataTypes[$columnName], ['integer', 'double'])) {
                $result[$columnName] = array_sum($columnValues) / count($columnValues);
            }
        }

        return $result;
    }

    public function max(): array
    {
        $result = [];

        $dataTypes = $this->data_type ?? $this->dtype();

        foreach ($this->data as $columnName => $columnValues) {
            if (in_array($dataTypes[$columnName], ['integer', 'double'])) {
                $result[$columnName] = max($columnValues);
            }
        }

        return $result;
    }

    public function min(): array
    {
        $result = [];

        $dataTypes = $this->data_type ?? $this->dtype();

        foreach ($this->data as $columnName => $columnValues) {
            if (in_array($dataTypes[$columnName], ['integer', 'double'])) {
                $result[$columnName] = min($columnValues);
            }
        }

        return $result;
    }

    public function sum(): array
    {
        $result = [];

        $dataTypes = $this->data_type ?? $this->dtype();

        foreach ($this->data as $columnName => $columnValues) {
            if (in_array($dataTypes[$columnName], ['integer', 'double'])) {
                $result[$columnName] = array_sum($columnValues);
            }
        }

        return $result;
    }

    public function groupBy(array $columns): GroupedPhanFrame
    {
        if (empty($columns)) {
            throw new InvalidArgumentException('At least one column must be specified for grouping.');
        }

        $groupedData = [];

        // Iterate over the data to group by the specified columns
        foreach ($this->data[$columns[0]] as $index => $value) {
            $key = [];
            foreach ($columns as $col) {
                $key[] = $this->data[$col][$index];
            }
            $key = implode('_', $key);

            foreach ($this->data as $col => $values) {
                if (!isset($groupedData[$key][$col])) {
                    $groupedData[$key][$col] = [];
                }
                $groupedData[$key][$col][] = $values[$index];
            }
        }

        return new GroupedPhanFrame($groupedData, $columns);
    }

    public function row(array $indexes): PhanFrame
    {
        $result = [];

        foreach ($indexes as $index) {
            $row = [];
            foreach ($this->data as $columnName => $columnValues) {
                $row[] = $columnValues[$index];
            }
            $result[] = $row;
        }

        return new self($result);
    }

    public function range(array $range): PhanFrame
    {
        list($start, $end) = $range;
        $result = [];

        for ($i = $start; $i <= $end; $i++) {
            $row = [];
            foreach ($this->data as $columnName => $columnValues) {
                $row[] = $columnValues[$i];
            }
            $result[] = $row;
        }

        return new self($result);
    }

    public function count(): array
    {
        $result = [];

        foreach ($this->data as $columnName => $columnValues) {
            $result[$columnName] = count(array_filter($columnValues, function ($value) {
                return $value !== null;
            }));
        }

        return $result;
    }

    public function countNulls(): array
    {
        $result = [];

        foreach ($this->data as $columnName => $columnValues) {
            $result[$columnName] = count(array_filter($columnValues, function ($value) {
                return $value === null;
            }));
        }

        return $result;
    }

    public function fillNa($value = 0): PhanFrame
    {
        $result = [];

        foreach ($this->data as $columnName => $columnValues) {
            $result[$columnName] = array_map(function ($item) use ($value) {
                return $item === null ? $value : $item;
            }, $columnValues);
        }

        return new self($result);
    }

    public function unique($column): array
    {
        if (!isset($this->data[$column])) {
            throw new InvalidArgumentException("Column '$column' not found in DataFrame");
        }

        return array_values(array_unique($this->data[$column]));
    }

    protected function formatTable(array $data): string
    {
        $table = '';

        // Find the maximum length for each column
        $columnLengths = array_map('max', array_map(function ($row) {
            return array_map('strlen', $row);
        }, $data));

        foreach ($data as $columnName => $columnValues) {
            $table .= str_pad($columnName, $columnLengths[$columnName] + 2);
        }

        $table .= PHP_EOL;

        for ($i = 0; $i < count(current($data)); $i++) {
            foreach ($data as $columnName => $columnValues) {
                $table .= str_pad($columnValues[$i], $columnLengths[$columnName] + 2);
            }
            $table .= PHP_EOL;
        }

        return $table;
    }
}