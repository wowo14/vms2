<?php
namespace app\widgets;
class DynamicDTCWidget extends BaseDynamicDataTableWidget
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
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            return '<input type="checkbox" class="row-select" value="' + full[Object.keys(full)[1]] + '">';
                        },
                        width: '10%',
                        title: '<input type="checkbox" id="checkAll"> All '
                    }
                ].concat($columnsJson),
                order: $defaultOrderJson,
                destroy: true
            });
            $('input.row-select').on('click', function() {//test
                console.log(this);
                var checkbox=$(this);
                var isChecked = checkbox.is(':checked');
                checkbox.prop('checked', !isChecked);
                $(this).closest('tr').toggleClass('selected', !isChecked);
            });
            $('#$tableId tbody').on('click', 'tr', function() {
                var checkbox = $(this).find('.row-select');
                var isChecked = checkbox.prop('checked');
                checkbox.prop('checked', !isChecked);
                $(this).toggleClass('selected', !isChecked);
            });
            $('#checkAll').on('click', function() {
                var isChecked = $(this).is(':checked');
                $('#$tableId tbody input.row-select').prop('checked', isChecked);
                $('#$tableId tbody tr').toggleClass('selected', isChecked);
            });
            $('#apply-filters-{$this->id}').on('click', function() {
                table.ajax.reload();
            });
        }
        $('#apply-selection-{$this->id}').on('click', function() {
            var selectedValues = [];
            $('#$tableId tbody input.row-select:checked').each(function() {
                selectedValues.push($(this).val());
            });
            $('#{$this->options['id']}').val(selectedValues.join(', '));
            $('#{$this->options['id']}-hidden').val(selectedValues.join(', '));
            $('#$modalId').modal('hide');
            $('#{$this->options['id']}').trigger('change');
        });
        JS;
    }
}
