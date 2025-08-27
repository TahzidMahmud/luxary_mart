<script>
    "use strict";

    ATE.searchKey = '';

    // submit form
    $('.update-order-form').on('submit', function(e) {
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
    $('.update-order-product-search-input').on('click', function() {
        debouncedProductSearch()
        $(".update-order-product-search").show();
    });

    // search products
    const debouncedProductSearch = debounce(getProducts, 300);
    $('.update-order-product-search-input').on('input', ((e) => {
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
            url: '{{ route(routePrefix() . '.update-orders.getProducts') }}',
            data: {
                searchKey: ATE.searchKey,
                selectedVariationIds: selectedVariationIds,
                warehouseId: warehouseId,
            },
            success: function(data) {
                $('.update-order-search-results').html(data);
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
                $(".update-order-product-search-input input").focus();
                $('.update-order-search-results').append(data);
            }
        });
    }

    // hadnle po product click
    function handleUpdateOrderProductClick($this) {
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

        $('.update-orders-tbody .no-data')?.remove();

        variationId = $($this).data('variation-id');
        length = $('.update-orders-tbody').children().length + 1;
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
                         <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground tr-current-stock"> 
                            ${stock} <span class="tr-unit">${unit}</span>
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[150px]">
                            <x-backend.inputs.number name="stockQty[]" min="1" value="1" />
                        </td> 
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                            <button type="button" class="text-md flex items-center justify-center"
                                onclick="removeOrderItem(this)">
                                <i class="fas fa-times text-red-500"></i>
                            </button>
                        </td>
                    </tr>`;

        $('.update-orders-tbody').append(tr);
        $($this).remove();
        $(".update-order-product-search").hide();
    }


    // removeOrderItem(this)
    function removeOrderItem($this) {
        $($this).closest('tr').remove();
    }
</script>
