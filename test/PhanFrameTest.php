<?php

namespace PhanFrame;

use PHPUnit\Framework\TestCase;

class PhanFrameTest extends TestCase
{
    public function test_can_print_phanframe()
    {
        $phanframe = PhanFrame::fromCsv('test/data/sales_data.csv');
        $this->assertStringContainsString('product', $phanframe->__toString());
    }

    public function test_can_create_phanframe_from_csv()
    {
        $phanframe = PhanFrame::fromCsv('test/data/sales_data.csv');
        $this->assertInstanceOf(PhanFrame::class, $phanframe);
    }

    public function test_can_select_columns()
    {
        $phanframe = PhanFrame::fromCsv('test/data/sales_data.csv');
        $selected_phanframe = $phanframe->selectColumns(['product', 'unit_price']);
        $this->assertInstanceOf(PhanFrame::class, $selected_phanframe);
    }

    public function test_can_group_by()
    {
        $phanframe = PhanFrame::fromCsv('test/data/sales_data.csv');
        $grouped_phanframe = $phanframe->groupBy(['product']);
        $this->assertInstanceOf(GroupedPhanFrame::class, $grouped_phanframe);
    }

    public function test_can_sum_grouped_phanframe()
    {
        $phanframe = PhanFrame::fromCsv('test/data/sales_data.csv');
        $grouped_phanframe = $phanframe->groupBy(['product']);
        $summed_phanframe = $grouped_phanframe->sum('unit_price');
        $this->assertInstanceOf(PhanFrame::class, $summed_phanframe);
    }

    public function test_can_max_grouped_phanframe()
    {
        $phanframe = PhanFrame::fromCsv('test/data/sales_data.csv');
        $grouped_phanframe = $phanframe->groupBy(['product']);
        $maxed_phanframe = $grouped_phanframe->max('unit_price');
        $this->assertInstanceOf(PhanFrame::class, $maxed_phanframe);
    }

    public function test_can_min_grouped_phanframe()
    {
        $phanframe = PhanFrame::fromCsv('test/data/sales_data.csv');
        $grouped_phanframe = $phanframe->groupBy(['product']);
        $mined_phanframe = $grouped_phanframe->min('unit_price');
        $this->assertInstanceOf(PhanFrame::class, $mined_phanframe);
    }

}