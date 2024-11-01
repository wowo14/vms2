<?php
use app\models\PaketPengadaan;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use kartik\editable\Editable;
$idmodelnego = "negodetails";
$editpenawaran = [[
    'class' => 'kartik\grid\EditableColumn',
    'attribute' => 'penawaran',
    'editableOptions' => [
        'header' => 'Penawaran',
        'asPopover' => false,
        'inputType' => Editable::INPUT_TEXT,
        'options' => [
            'class' => 'form-control',
            'pluginOptions' => [
                'autoclose' => true,
            ]
        ],
        'formOptions' => ['action' => ['/paketpengadaan/editablepenawaran']]
    ],
]];
$editadmin = [
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'penawaran',
        'editableOptions' => [
            'header' => 'Penawaran',
            'asPopover' => false,
            'inputType' => Editable::INPUT_TEXT,
            'options' => [
                'class' => 'form-control',
                'pluginOptions' => [
                    'autoclose' => true,
                ]
            ],
            'formOptions' => ['action' => ['/paketpengadaan/editablepenawaran']]
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'negosiasi',
        'editableOptions' => [
            'header' => 'Nego',
            'asPopover' => false,
            'inputType' => Editable::INPUT_TEXT,
            'options' => [
                'class' => 'form-control',
                'pluginOptions' => [
                    'autoclose' => true,
                ]
            ],
            'formOptions' => ['action' => ['/paketpengadaan/editablenego']]
        ],
    ]
];
$col = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'nama_produk',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'qty',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'volume',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'satuan',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'hps_satuan',
        'format' => 'currency',
        'contentOptions' => ['class' => 'text-right']
    ],
    [
        'attribute' => 'totalhps',
        'format' => 'raw',
        'value' => fn($d) => Yii::$app->formatter->asCurrency(($d->qty ?? 1) * ($d->volume ?? 1) * $d->hps_satuan),
        'contentOptions' => ['class' => 'text-right'],
        'pageSummary' => true,
        'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
        'pageSummaryFunc' => function ($data) {
            return Yii::$app->tools->sumCurrency($data);
        },
    ],
    [
        'attribute' => 'totalpenawaran',
        'format' => 'raw',
        'value' => fn($d) => Yii::$app->formatter->asCurrency(($d->qty ?? 1) * ($d->volume ?? 1) * (Yii::$app->tools->reverseCurrency($d->penawaran))),
        'contentOptions' => ['class' => 'text-right'],
        'pageSummary' => true,
        'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
        'pageSummaryFunc' => function ($data) {
            return Yii::$app->tools->sumCurrency($data);
        },
    ],
    [
        'attribute' => 'totalnegosiasi',
        'format' => 'raw',
        'value' => fn($d) => Yii::$app->formatter->asCurrency(($d->qty ?? 1) * ($d->volume ?? 1) * (Yii::$app->tools->reverseCurrency($d->negosiasi))),
        'contentOptions' => ['class' => 'text-right'],
        'pageSummary' => true,
        'pageSummaryOptions' => ['class' => 'auto unitsum', 'style' => 'text-align:right;'],
        'pageSummaryFunc' => function ($data) {
            return Yii::$app->tools->sumCurrency($data);
        },
    ],
];
$colCollection = collect($col);
$totalHpsIndex = $colCollection->search(function ($column) {
    return is_array($column) && isset($column['attribute']) && $column['attribute'] === 'totalhps';
});
if ($totalHpsIndex !== false) {
    if (Yii::$app->tools->isAdmin() || PaketPengadaan::isPP()) {
        $colCollection = $colCollection->slice(0, $totalHpsIndex)
            ->concat($editadmin)
            ->concat($colCollection->slice($totalHpsIndex));
    } elseif (Yii::$app->tools->isVendor()) {
        $colCollection = $colCollection->slice(0, $totalHpsIndex)
            ->concat($editpenawaran)
            ->concat($colCollection->slice($totalHpsIndex));
    }
}
$col = $colCollection->toArray();
return $col;
Modal::begin([
    "id" => $idmodelnego,
    "footer" => "",
    "size" => "modal-xl",
    "clientOptions" => [
        "tabindex" => false,
        "backdrop" => "static",
        "keyboard" => true,
        "focus" => true,
    ],
    "options" => [
        "tabindex" => true
    ]
]);
