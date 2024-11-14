<?php
namespace app\widgets;
use yii\base\Widget;
class PivotReport extends Widget
{
    public $titles;
    public $xColName;
    public $yColName;
    public $totalColName;
    public $pivotData;
    public $colXData = [];
    public $colYData = [];
    public function __construct(string $titles,string $xColName, string $yColName, string $totalColName, array $data)
    {
        $this->titles = $titles;
        $this->xColName = $xColName;
        $this->yColName = $yColName;
        $this->totalColName = $totalColName;
        $this->pivotData = $data;
        $this->_assignData();
    }
    public function getData($xValue, $yValue)
    {
        foreach ($this->pivotData as $data) {
            if ($data[$this->xColName] == $xValue && $data[$this->yColName] == $yValue) {
                return $data[$this->totalColName];
            }
        }
        return 0;
    }
    public function totalX($value)
    {
        return $this->_total('xColName', $value);
    }
    public function totalY($value)
    {
        return $this->_total('yColName', $value);
    }
    public function grandTotal()
    {
        $total = 0;
        foreach ($this->pivotData as $data) {
            $total += $data[$this->totalColName];
        }
        return $total;
    }
    public function generateHtml($options)
    {
        ?>
        <table border="0" class="<?= $options['class'] ?>">
            <tr>
                <th>#</th>
                <th><?=$this->titles?></th>
                <?php foreach ($this->colXData as $xValue) : ?>
                    <?= '<th class="header">' . $xValue . '</th>' ?>
                <?php endforeach; ?>
                <th>Total</th>
            </tr>
            <?php foreach ($this->colYData as $k=>$yValue) : ?>
                <?= '<tr><td>' . ($k+1) . '</td><td>' . $yValue . '</td>' ?>
                <?php foreach ($this->colXData as $xValue) : ?>
                    <?= '<td class="data">' . $this->getData($xValue, $yValue). '</td>' ?>
                <?php endforeach; ?>
                <?= '<td class="data">' . $this->totalY($yValue) . '</td></tr>' ?>
            <?php endforeach; ?>
            <tr>
                <th colspan="2">Total</th>
                <?php foreach ($this->colXData as $xValue) : ?>
                    <th class="data"><?= $this->totalX($xValue) ?></th>
                <?php endforeach; ?>
                <th class="data"><?= $this->grandTotal() ?></th>
            </tr>
        </table>
        <?php
    }
    function generateCsv($filename = null, $delimiter = ',')
    {
        if (empty($filename)) {
            $filename = 'csv_download_' . date('YmdHis') . '.csv';
        }
        ob_end_clean();
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        $f = fopen('php://output', 'w');
        //create header
        fputcsv($f, array_merge([''], $this->colXData, ['total']), $delimiter);
        //create data
        foreach ($this->colYData as $yValue) {
            $row = [];
            $row[] = $yValue;
            foreach ($this->colXData as $xValue) {
                $row[] = $this->getData($xValue, $yValue);
            }
            $row[] = $this->totalY($yValue);
            fputcsv($f, $row, $delimiter);
        }
        //create summary row
        $summaryRow = [];
        $summaryRow[] = 'Total';
        foreach ($this->colXData as $xValue) {
            $summaryRow[] = $this->totalX($xValue);
        }
        $summaryRow[] = $this->grandTotal();
        fputcsv($f, $summaryRow, $delimiter);
        exit();
    }
    protected function _total(string $colNameType, $value)
    {
        $total = 0;
        foreach ($this->pivotData as $data) {
            if ($data[$this->{$colNameType}] == $value) {
                $total += $data[$this->totalColName];
            }
        }
        return $total;
    }
    protected function _assignData()
    {
        foreach ($this->pivotData as $data) {
            $this->colXData[] = $data[$this->xColName];
            $this->colYData[] = $data[$this->yColName];
        }
        $this->colXData = array_values(array_unique($this->colXData));
        $this->colYData = array_values(array_unique($this->colYData));
    }
}
