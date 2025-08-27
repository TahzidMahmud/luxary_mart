<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/swiper.min.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('js/nouislider.min.js') }}"></script>
<script src="{{ asset('js/uppy.min.js') }}"></script>
<script src="{{ asset('js/micromodal.min.js') }}"></script>
<script src="{{ asset('js/jodit.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/footable.min.js') }}"></script>
<script src="{{ asset('js/toastr.min.js') }}"></script>
<script src="{{ asset('js/jquery.countdown.min.js') }}"></script>
<script src="{{ asset('js/accodion.min.js') }}"></script>
{{-- floating ui js files --}}
<script src="{{ asset('js/floating-ui-core-1.6.0.min.js') }}"></script>
<script src="{{ asset('js/floating-ui-dom-1.6.3.min.js') }}"></script>

<script>
    "use strict";

    ATE.local = {
        nothing_selected: '{!! translate('Nothing selected', null, true) !!}',
        nothing_found: '{!! translate('Nothing found', null, true) !!}',
        choose_file: '{{ translate('Choose file') }}',
        file_selected: '{{ translate('File selected') }}',
        files_selected: '{{ translate('Files selected') }}',
        add_more_files: '{{ translate('Add more files') }}',
        adding_more_files: '{{ translate('Adding more files') }}',
        drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
        browse: '{{ translate('Browse') }}',
        upload_complete: '{{ translate('Upload complete') }}',
        upload_paused: '{{ translate('Upload paused') }}',
        resume_upload: '{{ translate('Resume upload') }}',
        pause_upload: '{{ translate('Pause upload') }}',
        retry_upload: '{{ translate('Retry upload') }}',
        cancel_upload: '{{ translate('Cancel upload') }}',
        uploading: '{{ translate('Uploading') }}',
        processing: '{{ translate('Processing') }}',
        complete: '{{ translate('Complete') }}',
        file: '{{ translate('File') }}',
        files: '{{ translate('Files') }}',
    }

    // change language
    function changeLocaleLanguage(e) {
        var locale = e.dataset.flag;
        $.post("{{ route(routePrefix() . '.changeLanguage') }}", {
            _token: '{{ csrf_token() }}',
            locale: locale
        }, function(data) {
            location.reload();
        });
    }

    ATE.data = {
        csrf: '{{ csrf_token() }}',
        appUrl: '{{ url('/') }}',
        fileBaseUrl: $('meta[name="file-base-url"]').attr("content"),
    };

    const dateFormatter = (date, formatString = 'DD MMM, YYYY') => {
        return moment(date).format(formatString);
    }

    const debounce = function(func, wait, immediate) {
        let timeout;
        return function() {
            let context = this,
                args = arguments;
            let later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            let callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args)
        }
    }

    // category tree for product edit page
    function initCategoryTree() {
        $('.has-subcategory .category-toggler').on('click', function() {
            $(this).parent().toggleClass('open');
            $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
        })
    }
    initCategoryTree();

    // init accordion
    function initAccordion() {
        const accordionMarkup = document.querySelectorAll('.init-accordion')
        if (accordionMarkup.length) {
            new Accordion(Array.from(accordionMarkup), {
                duration: 200,
                showMultiple: true,
                // openOnInit: [0]
            });
        }
    }
    initAccordion();


    // ajax toast 
    function notifyMe(level, message) {
        if (level == 'danger') {
            level = 'error';
        }
        toastr.options = {
            closeButton: true,
            newestOnTop: false,
            progressBar: true,
            positionClass: "toast-top-right",
            preventDuplicates: false,
            onclick: null,
            showDuration: "3000",
            hideDuration: "1000",
            timeOut: "3000",
            extendedTimeOut: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut",
        };
        toastr[level](message);
    }

    // laravel flash toast messages 
    @foreach (session('flash_notification', collect())->toArray() as $message)
        notifyMe("{{ $message['level'] }}", "{{ $message['message'] }}");
    @endforeach

    // update is_active status
    function initActiveStatusUpdate() {
        $("input[name=isActiveCheckbox]").on('change', async function() {
            let data = {
                _token: '{{ csrf_token() }}',
                id: $(this).val(),
                isActive: $(this).data('status') == 1 ? 0 : 1,
            }
            const res = await $.ajax({
                url: `${$(this).data('route')}`,
                type: "POST",
                data: data,
            });

            if (res.success == true && res.status == 200) {
                notifyMe('success', res.message);
            } else {
                notifyMe('success', `{{ translate('Something went wrong') }}`);
            }
        })
    }
    initActiveStatusUpdate();

    // update is_default status
    function initDefaultStatusUpdate() {
        $("input[name=isDefaultCheckbox]").on('change', async function() {
            let data = {
                _token: '{{ csrf_token() }}',
                id: $(this).val(),
                isDefault: $(this).data('status') == 1 ? 0 : 1,
            }
            const res = await $.ajax({
                url: `${$(this).data('route')}`,
                type: "POST",
                data: data,
            });

            if (res.success == true && res.status == 200) {
                notifyMe('success', res.message);
                window.location.reload();
            } else {
                notifyMe('success', `{{ translate('Something went wrong') }}`);
            }
        })
    }
    initDefaultStatusUpdate();

    // init confrim modal
    function initConfirmModal() {
        $('.confirm-modal').on('click', function() {
            let url = $(this).data('href');
            let title = $(this).data('title');
            let text = $(this).data('text');
            let method = $(this).data('method');
            $('#confirm-modal form').attr('action', url);
            $('#confirm-modal-title').html(title);
            $('#confirm-modal-text').html(text);
            $('#confirm-modal-method').val(method);
        })
    }
    initConfirmModal();

    // initOrderAddressModal 
    function initOrderAddressModal() {
        $('.order-address-modal').on('click', function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let address = $(this).data('address');

            $('.order-id').val(id);
            $('.address-type').val(type);
            $("[name='address'").val(address);

            if (type == "shipping_address") {
                let direction = $(this).data('direction');
                let phone = $(this).data('phone');
                let title = '{{ translate('Update Shipping Address') }}'
                $('#order-address-modal-title').html(title);
                $('.address-info').removeClass('hidden');

                $("[name='direction'").val(direction);
                $("[name='phone'").val(phone);
            } else {
                let title = '{{ translate('Update Billing Address') }}'
                $('#order-address-modal-title').html(title);
                $('.address-info').addClass('hidden');
            }
        })
    }
    initOrderAddressModal();

    // init payment form modal
    function initPaymentModal() {
        $('.purchase-order-payment-modal').on('click', function() {
            let payable_id = $(this).data('payable-id');
            let payable_type = $(this).data('payable-type');
            let payable_amount = $(this).data('payable-amount');

            $("input[name='payable_id'").val(payable_id);
            $("input[name='payable_type'").val(payable_type);
            $("input[name='payable_amount'").val(payable_amount);
            $("input[name='paid_amount'").attr('max', payable_amount);
        })
    }
    initPaymentModal();

    // init payment histories modal
    function initPaymentHistoriesModal() {
        $('.purchase-order-payment-histories-modal').on('click', async function() {

            $('#purchase-order-payment-histories').empty();
            $('.loader').removeClass('hidden');

            let payable_id = $(this).data('payable-id');
            let payable_type = $(this).data('payable-type');

            let data = {
                _token: '{{ csrf_token() }}',
                payable_id: payable_id,
                payable_type: payable_type
            };

            // purchase-order-payment-histories
            const res = await $.ajax({
                url: '{{ route(routePrefix() . '.purchase-payments.index') }}',
                type: "POST",
                data: data,
            });

            $('#purchase-order-payment-histories').html(res);
            $('.loader').addClass('hidden');
        })
    }
    initPaymentHistoriesModal();

    // initFootable
    function initFootable() {
        $('.footable').footable({
            breakpoints: {
                xs: 480,
                sm: 768,
                md: 992,
                lg: 1200,
                xl: 1400,
                '2xl': 1600
            },
            on: {
                'ready.ft.table': function(e, ft) {
                    initActiveStatusUpdate();
                    initDefaultStatusUpdate();
                    initMicroModal();
                    initConfirmModal();
                    initPaymentModal();
                    initPaymentHistoriesModal();
                    initSelect2();
                    initUpdateQty();
                    mediaManager.getPreviouslySelectedMedia();
                    initSellerPayoutModal();
                }
            }
        });
    }
    initFootable();

    // item qty 
    function initUpdateQty() {
        $('.update_qty').each(function() {
            $(this).on('change', function() {
                var item_id = $(this).data('id');
                var qty = parseInt($(this).val() || 0);
                if (qty < 1) {
                    notifyMe('error', '{{ translate('Qty should be atleast 1') }}');
                    $(this).focus();
                } else {
                    let confirmation = confirm('{{ translate('Are you sure want to update?') }}')
                    if (confirmation) {
                        $.post('{{ route('admin.orders.updateQty') }}', {
                            _token: '{{ @csrf_token() }}',
                            id: item_id,
                            qty: qty
                        }, function(data) {
                            notifyMe('success',
                                '{{ translate('Qty has been updated') }}');
                            window.location.reload();
                        }).fail(function(xhr, status, error) {
                            var errorMessage = xhr
                                .responseJSON
                                .message;
                            notifyMe('error', 'Failed to update quantity: ' + errorMessage);
                        });
                    }
                }
            });
        })

    }
    initUpdateQty();

    // initSelect2
    function initSelect2() {

        if ($('.initSelect2').hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }

        $('.initSelect2').each(function() {
            const disableSearch = $(this).data('search'); // search disabled   

            const selectionCssClass = $(this).data('selection-css-class') ||
                "input-select2"; // or 'multi-select2' 

            let config = {
                selectionCssClass: selectionCssClass
            };

            if (disableSearch != undefined) {
                config.minimumResultsForSearch = -1;
            }
            $(this).select2(config);

            // fix:: this removes multiple select box (footable)
            $(this).siblings(".select2-container--default").not(':first').remove();
        })
    }
    initSelect2();


    // language badge select2
    function initBadgeSelect2() {
        $(".badge-select").select2({
            selectionCssClass: 'multi-select2',
            templateResult: bgSelect,
            templateSelection: bgSelect,
            escapeMarkup: function(m) {
                return m;
            },
        });

        function bgSelect(state) {
            var bg = $(state.element).data("bg");
            if (!bg) return state.text;
            return (
                "<div class='flex items-center justify-between w-full'><div class='me-2'>" + state.text +
                "</div><div class='rounded-full w-[18px] h-[18px]' style='background-color:" + bg +
                " !important'></div></div>"
            );
        }
    }
    initBadgeSelect2();


    // init modal
    function initMicroModal() {
        MicroModal.init({
            onShow: (modal, triggeredBy) => {
                if (modal.id === 'media-manager-modal') {
                    mediaManager.init(triggeredBy);
                }
            },
            onClose: (modal) => {
                if (modal.id === 'media-manager-modal') {
                    mediaManager.reset();
                }
            },
            awaitCloseAnimation: true,
            awaitOpenAnimation: true,
            debugMode: true
        });
    }
    initMicroModal();

    // navbar search
    ATE.searchKey = '';

    const debouncedSearch = debounce(getNavbarSearchData, 200);

    $('.navbar-search-input').on('input', ((e) => {
        ATE.searchKey = e.target.value
        debouncedSearch()
    }));

    // get Navbar Search Data
    function getNavbarSearchData() {
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: '{{ route(routePrefix() . '.getNavbarSearchData') }}',
            data: {
                search: ATE.searchKey,
            },
            success: function(data) {
                $('.navbar-search').html(data);
            }
        });
    }

    // initSellerPayoutModal 
    function initSellerPayoutModal() {
        $("[name='payment_method'").on('change', function() {
            if ($(this).val() == "cash") {
                $('.payment-details').addClass('hidden');
            } else {
                $('.payment-details').removeClass('hidden');
            }
        })
        $('.seller-payout-modal').on('click', function() {
            let id = $(this).data('id');
            let shopId = $(this).data('shop-id');
            let due = $(this).data('due');
            let demandedAmount = $(this).data('demanded-amount');
            let bankName = $(this).data('bank-name');
            let bankAccName = $(this).data('bank-acc-name');
            let bankAccNo = $(this).data('bank-acc-no');
            let bankRoutingNo = $(this).data('bank-routing-no');
            let cash = $(this).data('cash');
            let bank = $(this).data('bank');

            $('.due').html(due ? due : 0);
            $('.demanded').html(demandedAmount ? demandedAmount : 0);
            $("[name='amount'").val(demandedAmount ? demandedAmount : '');
            $("[name='id'").val(id ? id : null);

            $("[name='shop_id'").val(shopId);

            if (bank) {
                $('.bank').removeClass('hidden');
                $('.bank-details').removeClass('hidden');
                $('.bank-name').html(bankName);
                $('.bank-acc-name').html(bankAccName);
                $('.bank-acc-no').html(bankAccNo);
                $('.bank-routing-no').html(bankRoutingNo);
            } else {
                $('.bank').addClass('hidden');
                $('.bank-details').addClass('hidden');
            }

            if (cash) {
                $('.cash').removeClass('hidden');
            } else {
                $('.cash').addClass('hidden');
            }

        })
    }
    initSellerPayoutModal();

    // handle openAI content generation 
    function generateOpenAIContent(subject) {
        let title = $('input[name="name"]').val()

        if (!title) {
            title = $('input[name="title"]').val()
        }

        let prompt = 'Generate ' + subject + ' for ' + title

        const regex = new RegExp(`\/products\/`);
        if (subject == 'description' && location.pathname.match(regex)) {
            prompt = 'Generate ' + subject + ' for e-commerce website for the product' + title +
                ' including an introduction followed by some key features'
        }

        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                prompt: prompt
            },
            url: '{{ route('openai') }}',
            success: function(data) {
                if (subject == 'description' || subject == 'content') {
                    const editor = new Jodit('.jodit-editor')
                    editor.setEditorValue(data);
                } else {
                    $(`[name=${subject}]`).val(data)
                }
            }
        });
    }

    // add more
    $('[data-toggle="add-more"]').each(function() {
        var $this = $(this);
        var content = $this.data("content");
        var target = $this.data("target");

        $this.on("click", function(e) {
            e.preventDefault();
            $(target).append(content);
            initSelect2();
            initMicroModal();
            mediaManager.removeInputValue();
        });
    });

    // remove parent
    $(document).on(
        "click",
        '[data-toggle="remove-parent"]',
        function() {
            var $this = $(this);
            var parent = $this.data("parent");
            $this.closest(parent).remove();
        }
    );

    // homepage-form
    function initHomepageFormSubmit() {
        $('.homepage-form').each(function() {
            $(this).on('submit', function(e) {
                e.preventDefault();

                let $this = this;
                $($this).find('.submit-btn').attr('disabled', 'disabled');
                $($this).find('.submit-btn').html('{{ translate('Please Wait..') }}');

                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: $($this).serialize(),
                    url: '{{ route('admin.general-settings.update') }}',
                    success: function(data) {
                        if (data.success) {
                            notifyMe('success',
                                '{{ translate('Information updated successfully.') }}');
                            $($this).find('.submit-btn').removeAttr('disabled');
                            $($this).find('.submit-btn').html(
                                '{{ translate('Save Changes') }}');

                            // Update iframe source
                            $('#homepage-iframe').attr('src', '{{ url('/') }}');
                        } else {
                            notifyMe('error', '{{ translate('Something went wrong') }}');
                        }
                    }
                });
            })
        })
    }
    initHomepageFormSubmit()

    // roles permissions

    // check full access checkbox if all are checked under this
    function initPermissionFullAccessCheck() {
        $('.permission-group-tr').each(function() {
            let checkedLength = $(this).find('.permissions-selection').find('input[type="checkbox"]:checked')
                .length;
            let allLength = $(this).find('.permissions-selection').find('input[type="checkbox"]').length;
            if (checkedLength == allLength) {
                $(this).find('input[name="full_access"]').prop('checked', 'true');
            } else {
                $(this).find('input[name="full_access"]').prop('checked', false);
            }
        })
    }
    initPermissionFullAccessCheck();

    // toggle Group all
    function toggleGroupAll($this) {
        $($this).closest('.permission-group-tr').find("input:checkbox").prop('checked', $this.checked);
    }
</script>

@vite('resources/backend/js/main.js')
@vite('resources/backend/js/pusher.js')
