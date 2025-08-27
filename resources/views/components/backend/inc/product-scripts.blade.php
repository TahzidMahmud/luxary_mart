<script>
    "use strict";

    // switch markup based on selection
    function isVariantProduct(el) {
        $(".hasVariation").hide();
        $(".noVariation").hide();

        if ($(el).is(':checked')) {
            $(".hasVariation").show();
            generateVariationCombinations();

            // remove required field for non variations
            $("#price").removeAttr('required', true);
            $("#stock_qty").removeAttr('required', true);
            $("#sku").removeAttr('required', true);

            // add required field for variations  
            $(".chosen_variations").attr('required', true);

        } else {
            $(".noVariation").show();
            // add required field for non variations  
            $("#price").attr('required', true);
            $("#stock_qty").attr('required', true);
            $("#sku").attr('required', true);

            // remove required field for variations  
            $(".chosen_variations").removeAttr('required', true);
        }
    }

    // variation combinations
    function generateVariationCombinations() {
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: '{{ route(routePrefix() . '.products.generateVariationCombinations') }}',
            data: $('#product-form').serialize(),
            success: function(data) {
                $('#variation_combination').html(data);
                initAccordion();
                initMicroModal();
            }
        });
    }


    // get values for selected variations
    function getVariationValues(e) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: "POST",
            data: {
                variation_id: $(e).val()
            },
            url: '{{ route(routePrefix() . '.products.getVariationValues') }}',
            success: function(data) {
                $(e).closest('.grid').find('.variationvalues').html(data);
                generateVariationCombinations();
                initSelect2();
            }
        });
    }


    // add another variation
    function addAnotherVariation() {
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: $('#product-form').serialize(),
            url: '{{ route(routePrefix() . '.products.newVariation') }}',
            success: function(data) {
                if (data.count > 0) {
                    $('.chosen_variation_options').append(data.view);
                    initSelect2();
                } else if (data.count == -1) {
                    notifyMe('warning', '{{ translate('Please select a variation.') }}');
                }
            }
        });
    }

    // remove variation
    function removeVariation($this) {
        $this.closest('.grid').remove();
        generateVariationCombinations();
    }

    // category-selector 
    $(".category-selector:checkbox").change(function() {
        let checked = this.checked;
        $(this).closest('.category').find('input[type="checkbox"]').attr('checked', checked);
    });

    // initCategoryCreateModal
    function initCategoryCreateModal() {
        $('.category-create-modal').on('click', async function() {

            $('#category-create').empty();
            $('.loader').removeClass('hidden');

            let data = {
                _token: '{{ csrf_token() }}',
            };

            // category-create
            const res = await $.ajax({
                url: '{{ route('admin.categories.initCreateModal') }}',
                type: "GET",
                data: data,
            });

            $('#category-create').html(res);
            $('.loader').addClass('hidden');
            initSelect2();
            handleCategoryCreateFormSubmit()
        })
    }
    initCategoryCreateModal();

    // handle category form submit
    function handleCategoryCreateFormSubmit() {
        $('.category-form-modal').on('submit', function(e) {
            e.preventDefault();

            // Get all checked category IDs
            var checkedCategoryIds = $('input[name="category_ids[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            // Convert the array to a string representation (comma-separated values)
            var checkedCategoryIds = checkedCategoryIds.join(',');

            // do what you like with the input
            let $input = $('<input type="hidden" name="selected_categories"/>').val(checkedCategoryIds);
            // append to the form
            $('.category-form-modal').append($input);

            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $('.category-form-modal').serialize(),
                url: '{{ route('admin.categories.store') }}',
                success: function(data) {
                    $('.category-tree-data').empty().html(data);
                    initCategoryTree();
                    MicroModal.close('category-create-modal');
                    notifyMe('success', '{{ translate('Category added successfully.') }}');
                }
            });
        })
    }

    // handle brand form submit
    function handleBrandCreateFormSubmit() {
        $('.brand-form-modal').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $('.brand-form-modal').serialize(),
                url: '{{ route('admin.brands.store') }}',
                success: function(data) {
                    $('.brand-tree-data').empty().html(data);
                    MicroModal.close('brand-create-modal');
                    notifyMe('success', '{{ translate('Brand added successfully.') }}');
                }
            });
        })
    }
    handleBrandCreateFormSubmit();

    // handle unit form submit
    function handleUnitCreateFormSubmit() {
        $('.unit-form-modal').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $('.unit-form-modal').serialize(),
                url: '{{ route('admin.units.store') }}',
                success: function(data) {
                    $('.unit-tree-data').empty().html(data);
                    initSelect2();
                    MicroModal.close('unit-create-modal');
                    notifyMe('success', '{{ translate('Unit added successfully.') }}');
                }
            });
        })
    }
    handleUnitCreateFormSubmit();

    // handle tag form submit
    function handleTagCreateFormSubmit() {
        $('.tag-form-modal').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $('.tag-form-modal').serialize(),
                url: '{{ route('admin.tags.store') }}',
                success: function(data) {
                    $('.tag-tree-data').empty().html(data);
                    initSelect2();
                    MicroModal.close('tag-create-modal');
                    notifyMe('success', '{{ translate('Tag added successfully.') }}');
                }
            });
        })
    }
    handleTagCreateFormSubmit();

    // handle badge form submit
    function handleBadgeCreateFormSubmit() {
        $('.badge-form-modal').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $('.badge-form-modal').serialize(),
                url: '{{ route('admin.badges.store') }}',
                success: function(data) {
                    $('.badge-tree-data').empty().html(data);
                    MicroModal.close('badge-create-modal');
                    notifyMe('success', '{{ translate('Badge added successfully.') }}');
                }
            });
        })
    }
    handleBadgeCreateFormSubmit();
</script>
