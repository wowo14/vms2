<?php
namespace app\widgets;
class DynamicDataTableWidget extends BaseDynamicDataTableWidget
{
    protected function getJs($modalId, $tableId, $columnsJson, $ajaxUrl, $authToken, $multiple, $defaultOrderJson)
    {
        $initFunctionName = 'inittb' . $this->id;
        return <<<JS
        var table;
        $('#{$this->options['id']}').on('click', function() {
            $('#$modalId').modal('show')
                .find('.modal-body')
                .html('<table id="$tableId" class="table table-bordered" width="100%"></table>');
            $initFunctionName();
        });
        function $initFunctionName() {
            table = $('#$tableId').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '$ajaxUrl',
                    beforeSend: function (request) {
                        if ($authToken) {
                            request.setRequestHeader("Authorization", "Bearer " + $authToken);
                        }
                    },
                    data: function(d) {
                        $('.filter-input').each(function() {
                            d[$(this).attr('name')] = $(this).val();
                        });
                    },
                    dataSrc: 'data'
                },
                columns: $columnsJson,
                order: $defaultOrderJson,
                destroy: true,
                select: $multiple ? { style: 'multi' } : true
            });
            $('#$tableId tbody').on('click', 'tr', function() {
                if ($multiple) {
                    $(this).toggleClass('selected');
                } else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                    var selectedData = table.row(this).data();
                    $('#{$this->options['id']}').val(selectedData[Object.keys(selectedData)[1]]);
                    $('#$modalId').modal('hide');
                    $('#{$this->options['id']}').trigger('change');
                }
            });
            $('#apply-filters-{$this->id}').on('click', function() {
                table.ajax.reload();
            });
        }
        $('#apply-selection-{$this->id}').on('click', function() {
            var selectedData = table.rows('.selected').data().toArray();
            var selectedValues = selectedData.map(function(row) {
                return row[Object.keys(row)[1]];
            });
            $('#{$this->options['id']}').val(selectedValues.join(', '));
            $('#{$this->options['id']}-hidden').val(selectedValues.join(', '));
            $('#$modalId').modal('hide');
            $('#{$this->options['id']}').trigger('change');
        });
        JS;
    }
}