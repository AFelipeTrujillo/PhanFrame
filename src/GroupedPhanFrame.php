<?php

namespace PhanFrame;

class GroupedPhanFrame
{
    private $data;
    private $groupByColumns;

    public function __construct($data, $groupByColumns)
    {
        $this->data = $data;
        $this->groupByColumns = $groupByColumns;
    }

    public function sum($column): PhanFrame
    {
        $resultData = [];
        foreach ($this->data as $groupKey => $groupValues) {
            $sumValue = array_sum($groupValues[$column]);
            $resultData[$groupKey] = $sumValue;
        }

        // Convert to DataFrame format
        $finalData = [];
        foreach ($this->groupByColumns as $col) {
            $finalData[$col] = array_keys($resultData);
        }
        $finalData[$column] = array_values($resultData);

        return new PhanFrame($finalData);
    }

    public function max($column): PhanFrame
    {
        $resultData = [];
        foreach ($this->data as $groupKey => $groupValues) {
            $maxValue = max($groupValues[$column]);
            $resultData[$groupKey] = $maxValue;
        }

        // Convert to DataFrame format
        $finalData = [];
        foreach ($this->groupByColumns as $col) {
            $finalData[$col] = array_keys($resultData);
        }
        $finalData[$column] = array_values($resultData);

        return new PhanFrame($finalData);
    }

    public function min($column): PhanFrame
    {
        $resultData = [];
        foreach ($this->data as $groupKey => $groupValues) {
            $minValue = min($groupValues[$column]);
            $resultData[$groupKey] = $minValue;
        }

        // Convert to DataFrame format
        $finalData = [];
        foreach ($this->groupByColumns as $col) {
            $finalData[$col] = array_keys($resultData);
        }
        $finalData[$column] = array_values($resultData);

        return new PhanFrame($finalData);
    }

    public function __toString(): string
    {
        return json_encode($this->data);
    }
}