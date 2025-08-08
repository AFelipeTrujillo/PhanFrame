# PhanFrame

A PHP data manipulation library inspired by Pandas, designed for handling tabular data with ease. PhanFrame provides powerful tools for data analysis, transformation, and aggregation operations on structured datasets.

## Features

- **CSV File Reading**: Automatic CSV parsing with delimiter detection and data type conversion
- **Data Manipulation**: Select columns, filter rows, and transform data structures
- **Statistical Operations**: Calculate mean, max, min, sum, and count operations
- **Grouping Operations**: Group data by columns and perform aggregations
- **Data Inspection**: View data heads, tails, and analyze data types
- **Missing Data Handling**: Fill null values and count missing data
- **Memory Efficient**: Optimized for handling large datasets

## Installation

Clone this repository or include the source files in your PHP project:

```bash
git clone https://github.com/your-username/PhanFrame.git
```

## Requirements

- PHP 7.4 or higher
- No external dependencies required

## Quick Start

### Reading CSV Files

```php
<?php
require_once 'src/PhanFrame.php';
require_once 'src/CsvReader.php';
require_once 'src/GroupedPhanFrame.php';

use PhanFrame\PhanFrame;
use PhanFrame\CsvReader;

// Read CSV file with automatic delimiter detection
$df = PhanFrame::fromCsv('employees.csv');

// Read CSV with custom options
$df = PhanFrame::fromCsv('sales_data.csv', [
    'delimiter' => ',',
    'headers' => true,
    'encoding' => 'UTF-8'
]);
```

### Basic Data Operations

```php
// Display first 5 rows
echo $df;

// Get first 10 rows
$head = $df->head(10);

// Get last 5 rows
$tail = $df->tail(5);

// Select specific columns
$subset = $df->selectColumns(['name', 'salary', 'department']);

// Check data types
$types = $df->dtype();
print_r($types);
```

### Statistical Operations

```php
// Calculate statistics for numeric columns
$averageSalary = $df->mean();
$maxSalary = $df->max();
$minSalary = $df->min();
$totalSalaries = $df->sum();

// Count non-null values per column
$counts = $df->count();
echo "Total employees: " . $counts['name'] . "\n";

// Count null values per column
$nullCounts = $df->countNulls();
```

### Data Cleaning

```php
// Fill null salary values with 0
$cleanedDf = $df->fillNa(0);

// Fill null values with custom text
$cleanedDf = $df->fillNa('Unknown');

// Get unique departments
$departments = $df->unique('department');
print_r($departments);
```

### Grouping and Aggregation

```php
// Group by department
$groupedByDept = $df->groupBy(['department']);

// Calculate total salary by department
$salaryByDept = $groupedByDept->sum('salary');

// Find highest salary by department
$maxSalaryByDept = $groupedByDept->max('salary');

// Find lowest salary by department
$minSalaryByDept = $groupedByDept->min('salary');

echo $salaryByDept;
```

### Row Selection

```php
// Select specific employees by row index
$selectedEmployees = $df->row([0, 2, 4]);

// Select employees from row 10 to 20
$employeeRange = $df->range([10, 20]);

echo $selectedEmployees;
```

## API Reference

### PhanFrame Class

#### Static Methods
- `fromCsv(string $filePath, array $options = []): PhanFrame` - Create PhanFrame from CSV file

#### Data Inspection
- `head(int $rows = 5): array` - Get first n rows
- `tail(int $rows = 5): array` - Get last n rows
- `dtype(): array` - Get data types for each column
- `count(): array` - Count non-null values per column
- `countNulls(): array` - Count null values per column

#### Data Selection
- `selectColumns(array $columns): PhanFrame` - Select specific columns
- `row(array $indexes): PhanFrame` - Select rows by index
- `range(array $range): PhanFrame` - Select range of rows
- `unique(string $column): array` - Get unique values from column

#### Statistical Operations
- `mean(): array` - Calculate mean for numeric columns
- `max(): array` - Calculate maximum for numeric columns
- `min(): array` - Calculate minimum for numeric columns
- `sum(): array` - Calculate sum for numeric columns

#### Data Transformation
- `fillNa($value = 0): PhanFrame` - Fill null values
- `groupBy(array $columns): GroupedPhanFrame` - Group data by columns

### GroupedPhanFrame Class

#### Aggregation Methods
- `sum(string $column): PhanFrame` - Sum values by group
- `max(string $column): PhanFrame` - Maximum values by group
- `min(string $column): PhanFrame` - Minimum values by group

### CsvReader Class

#### Static Methods
- `read(string $filePath, array $options = []): PhanFrame` - Read CSV file

#### CSV Options
- `delimiter`: Field separator (auto-detected if not specified)
- `enclosure`: Field enclosure character (default: `"`)
- `escape`: Escape character (default: `\`)
- `headers`: Whether first row contains headers (default: `true`)
- `encoding`: File encoding (default: `UTF-8`)

## Examples

### Working with Employee Data

```php
use PhanFrame\PhanFrame;

// Load employee data
$employees = PhanFrame::fromCsv('employees.csv');

// View basic information
echo "Number of columns: " . count($employees->head(1)) . "\n";
echo "Data types:\n";
print_r($employees->dtype());

// Calculate department statistics
$deptStats = $employees->groupBy(['department'])->sum('salary');
echo "Total salary by department:\n";
echo $deptStats;

// Find highest paid employees by department
$maxSalaries = $employees->groupBy(['department'])->max('salary');

// Clean and prepare data
$cleanEmployees = $employees->fillNa(0)
                           ->selectColumns(['name', 'department', 'salary', 'hire_date']);
```

### Complete Data Analysis Workflow

```php
use PhanFrame\PhanFrame;

// 1. Load data
$df = PhanFrame::fromCsv('company_data.csv');

// 2. Explore data structure
$head = $df->head();
echo "First 5 rows:\n";
echo $df;

$types = $df->dtype();
echo "Data types:\n";
print_r($types);

$nulls = $df->countNulls();
echo "Missing values per column:\n";
print_r($nulls);

// 3. Clean data
$clean_df = $df->fillNa(0);
echo "Data cleaned - null values filled with 0\n";

// 4. Analyze by groups
$grouped = $clean_df->groupBy(['category']);
$summary = $grouped->sum('revenue');
echo "Revenue by category:\n";
echo $summary;

// 5. Get insights
$uniqueCategories = $clean_df->unique('category');
echo "Available categories:\n";
print_r($uniqueCategories);

$totalRevenue = $clean_df->sum();
echo "Total revenue: " . $totalRevenue['revenue'] . "\n";
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

If you encounter any issues or have questions, please open an issue on the GitHub repository.

## Roadmap

- [ ] Export to CSV functionality
- [ ] JSON import/export support
- [ ] More statistical functions (median, std deviation)
- [ ] Data filtering and querying capabilities
- [ ] Join operations between DataFrames
- [ ] Plotting and visualization features
