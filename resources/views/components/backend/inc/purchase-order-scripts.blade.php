<script>
    "use strict";

    ATE.searchKey = '';

    // submit form
    $('.po-form').on('submit', function(e) {
        let selectedVariationIds = $("input[name='selectedVariationIds[]']")
            .map(function() {
                return $(this).val();
            }).get();

        if (selectedVariationIds.length < 1) {
            notifyMe('error', '{{ translate('Please select at least one product') }}');
            e.preventDefault();
        }
    });

    // show search result ui
    $('.purchase-order-product-search-input').on('click', function() {
        debouncedProductSearch()
        $(".po-product-search").show();
    });

    // search products
    const debouncedProductSearch = debounce(getProducts, 300);
    $('.purchase-order-product-search-input').on('input', ((e) => {
        ATE.searchKey = e.target.value
        debouncedProductSearch()
    }));

    // get products
    function getProducts() {

        let warehouseId = ($('[name=warehouse_id]').val());
        if (warehouseId == '') {
            notifyMe('warning', '{{ translate('Please select warehouse') }}')
            return;
        }

        let selectedVariationIds = $("input[name='selectedVariationIds[]']")
            .map(function() {
                return $(this).val();
            }).get();

        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: '{{ route(routePrefix() . '.purchase-orders.getProducts') }}',
            data: {
                searchKey: ATE.searchKey,
                selectedVariationIds: selectedVariationIds,
                warehouseId: warehouseId,
            },
            success: function(data) {
                $('.po-search-results').html(data);
            }
        });
    }

    // load More Products
    function loadMorePoProducts($this, url) {
        $('.load-more-spin').removeClass('hidden');

        let selectedVariationIds = $("input[name='selectedVariationIds[]']")
            .map(function() {
                return $(this).val();
            }).get();

        let warehouseId = ($('[name=warehouse_id]').val());

        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: url,
            data: {
                selectedVariationIds: selectedVariationIds,
                warehouseId: warehouseId,
            },
            success: function(data) {
                $($this).closest('div').remove();
                $(".purchase-order-product-search-input input").focus();
                $('.po-search-results').append(data);
            }
        });
    }

    // hadnle po product click
    function handlePoProductClick($this) {
        let
            variationId = 0,
            length = 0,
            name = '',
            unit = '',
            defaultUnitPrice = 0,
            stock = 0,
            discount = 0,
            tax = 0,
            subtotal = 0;

        $('.purchase-orders-tbody .no-data')?.remove();

        variationId = $($this).data('variation-id');
        length = $('.purchase-orders-tbody').children().length + 1;
        name = $($this).data('name');
        unit = $($this).data('unit');
        stock = $($this).data('stock');
        defaultUnitPrice = $($this).data('price');
        discount = $($this).data('discount');
        tax = $($this).data('tax');
        subtotal = $($this).data('subtotal');

        let tr = `<tr class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                             <span class="tr-length">${length}</span>
                            <input type="hidden" name="selectedVariationIds[]" value="${variationId}"> 
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 w-[200px] tr-name">
                          ${name} 
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap tr-default-unit-price">
                            ${parseFloat(defaultUnitPrice).toFixed(3)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground tr-current-stock"> 
                            ${stock} <span class="tr-unit">${unit}</span>
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[150px]"> 
                            <x-backend.inputs.number name="unitPrice[]" min="0" onkeyup="calculateSubtotal(this)"
                                step="0.001" value="${defaultUnitPrice.toFixed(3)}" />
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[150px]">
                            <x-backend.inputs.number name="stockQty[]" min="1" value="1" onkeyup="calculateSubtotal(this)" />
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap">
                            <input type="hidden" name="discount[]" value="${discount}"> 
                            <input type="hidden" name="discountPrice[]" value="${discount}"> 
                            <span class="tr-discount">${parseFloat(discount).toFixed(3)}</span>
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap">
                            <input type="hidden" name="tax[]" value="${tax}">
                            <input type="hidden" name="taxPrice[]" value="${tax}">
                            <span class="tr-tax">${parseFloat(tax).toFixed(3)}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground"> 
                            <input type="hidden" name="subtotal[]" value="${subtotal}">
                            <span class="tr-subtotal">${parseFloat(subtotal).toFixed(3)}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                            <button type="button" class="text-md flex items-center justify-center"
                                onclick="removeOrderItem(this)">
                                <i class="fas fa-times text-red-500"></i>
                            </button>
                        </td>
                    </tr>`;

        $('.purchase-orders-tbody').append(tr);
        $($this).remove();
        $(".po-product-search").hide();
        calculateSummary();
    }

    // calculate subtotal 
    function calculateSubtotal($this) {
        let oldTr = $($this).closest('tr');
        let
            unitPrice = parseFloat($(oldTr).find('input[name="unitPrice[]"]').val()) || 0,
            stockQty = parseFloat($(oldTr).find('input[name="stockQty[]"]').val()) || 0,

            discount = parseFloat($(oldTr).find('input[name="discount[]"]').val()),
            tax = parseFloat($(oldTr).find('input[name="tax[]"]').val()),
            subtotal = (unitPrice * stockQty) + (tax * stockQty) - (discount * stockQty);

        $(oldTr).find("input[name='discountPrice[]']").val(discount * stockQty);
        $(oldTr).find('.tr-discount').html(parseFloat(discount * stockQty).toFixed(3));

        $(oldTr).find("input[name='taxPrice[]']").val(tax * stockQty);
        $(oldTr).find('.tr-tax').html(parseFloat(tax * stockQty).toFixed(3));

        $(oldTr).find("input[name='subtotal[]']").val(subtotal);
        $(oldTr).find('.tr-subtotal').html(parseFloat(subtotal).toFixed(3));
        calculateSummary();
    }

    // calculate summary
    function calculateSummary() {
        let taxPercentage = $('input[name="tax"]').val() || 0;
        let discount = $('input[name="discount"]').val() || 0;
        let shipping = $('input[name="shipping"]').val() || 0;

        let subtotal = $("input[name='subtotal[]']")
            .map(function() {
                return $(this).val();
            }).get();

        if (subtotal.length > 0) {
            subtotal = subtotal.reduce(function(a, b) {
                return (parseFloat(a) + parseFloat(b)).toFixed(3);
            });
        } else {
            subtotal = 0;
        }

        let subtotalWithTax = parseFloat(subtotal) + parseFloat((parseFloat(subtotal) * parseFloat(taxPercentage)) /
            100);

        let tax = parseFloat(subtotalWithTax - subtotal).toFixed(3);
        let grandTotal = (parseFloat(subtotalWithTax) - parseFloat(discount) + parseFloat(shipping)).toFixed(3);

        $('.tax-summary-percentage').html(taxPercentage);
        $('.tax-summary').html(tax);
        $('.discount-summary').html(parseFloat(discount).toFixed(3));
        $('.grand-total-summary').html(grandTotal)
    }

    // removeOrderItem(this)
    function removeOrderItem($this) {
        $($this).closest('tr').remove();
        calculateSummary();
    }

    // on warehouse change
    $('[name=warehouse_id]').on('change', function() {
        let noSearch = `<div class="flex justify-center items-center py-[35px]">
                        {{ translate('No results') }}
                    </div>`;
        let noProduct = `<tr class="bg-background no-data">
                            <td colspan="9"
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground text-center">
                                {{ translate('No data') }}
                            </td>
                        </tr>`;

        $('.purchase-orders-tbody').empty().html(noProduct);
        $('.po-search-results').empty().html(noSearch);
    })
</script>
