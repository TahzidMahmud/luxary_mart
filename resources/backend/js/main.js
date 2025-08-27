const { computePosition, flip, offset, shift } = window.FloatingUIDOM;

$(function () {
    const updateFloatingUIDropdown = (referenceEl, floatingEl, placement) => {
        computePosition(referenceEl, floatingEl, {
            placement,
            middleware: [offset(6), flip(), shift({ padding: 5 })],
        }).then(({ x, y }) => {
            Object.assign(floatingEl.style, {
                left: `${x}px`,
                top: `${y}px`,
            });
        });
    };

    $(document).on('focus', '.option-dropdown', function (e) {
        const referenceEl = $(this).find('.option-dropdown__toggler')[0];
        const floatingEl = $(this).find('.option-dropdown__options')[0];

        const placement = $(this).data('placement') || 'bottom-start';
        updateFloatingUIDropdown(referenceEl, floatingEl, placement);
    });

    // count down
    $('.count-down-time').each(function (_idx, item) {
        let countDownTime = $(item).data('endsAt');

        const counter = $(item).countdown(
            new Date(countDownTime),
            function (event) {
                $(this).html(
                    event.strftime(`
                    <div class="flex items-center gap-[2px]">
                        <div class="h-9 w-9 flex items-center justify-center bg-red-500 text-white">
                            %Dd
                        </div>
                        <span class="text-orange-500 font-bold">:</span>
                        <div class="h-9 w-9 flex items-center justify-center bg-red-500 text-white">
                            %Hh
                        </div>
                        <span class="text-orange-500 font-bold">:</span>
                        <div class="h-9 w-9 flex items-center justify-center bg-red-500 text-white">
                            %Mm
                        </div>
                        <span class="text-orange-500 font-bold">:</span>
                        <div class="h-9 w-9 flex items-center justify-center bg-red-500 text-white">
                            %Ss
                        </div>
                    </div>
                `),
                );
            },
        );
    });

    // language flag select2
    $('.flag-select2').select2({
        selectionCssClass: 'input-select2',
        templateResult: countryCodeFlag,
        templateSelection: countryCodeFlag,
        escapeMarkup: function (m) {
            return m;
        },
    });

    function countryCodeFlag(state) {
        var flagName = $(state.element).data('flag');
        if (!flagName) return state.text;
        return `<div class="flex items-center"><img class='me-2 h-3' src='${flagName}' /> ${state.text}</div>`;
    }

    // modal toggler
    $('.modal-toggler').on('click', function () {
        const target = $(this).data('target');
        $(target).toggleClass('hidden');
        $(this).toggleClass('active');
    });

    // toggle sidebar
    $('.sidebar-toggler').on('click', function () {
        if (window.innerWidth < 1024) {
            $('.sidebar').toggleClass('active');
            $('.overlay').toggleClass('active');
        } else {
            if ($('.sidebar').hasClass('in-active')) {
                $('.sidebar').animate({ width: '300px' }, 250);
            } else {
                $('.sidebar').animate({ width: '90px' }, 250);
            }

            $('.sidebar').toggleClass('in-active');
        }
    });
    $('.overlay').on('click', function () {
        if (window.innerWidth < 1024) {
            $('.sidebar').removeClass('active');
        } else {
            $('.sidebar').addClass('in-active');
        }
        $('.overlay').removeClass('active');
    });

    // toggle sidebar sub-menu
    $('.has-submenu .sidebar__item').on('click', function (e) {
        e.preventDefault();
        $(this).parent().toggleClass('expanded');
    });

    // hide sidebar when screen is too small
    const autoCloseSidebar = () => {
        if ($(window).width() < 1024) {
            $('.sidebar').addClass('sidebar--mobile');
        } else {
            $('.sidebar').removeClass('sidebar--mobile');
        }
    };

    window.addEventListener('resize', function () {
        autoCloseSidebar();
    });
    autoCloseSidebar();

    // date picker
    $('.date-picker.date-picker--range input').daterangepicker();
    $('.date-picker.date-picker--single input').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        drops: 'auto',
        autoApply: true,
        locale: {
            format: 'DD-MM-YYYY',
        },
    });

    const rangeSliders = document.querySelectorAll('.range-slider');
    rangeSliders.forEach((rangeSliderEl) => {
        const minInputEl = rangeSliderEl.querySelector('.range-slider__min');
        const maxInputEl = rangeSliderEl.querySelector('.range-slider__max');

        // initialize a noUiSlider for each
        const rangeSlider = noUiSlider.create(
            rangeSliderEl.querySelector('.range-slider__slider'),
            {
                range: {
                    min: 0,
                    max: 100,
                },
                tooltips: true,
                step: 1,
                connect: true,
                start: [30, 70],
            },
        );

        rangeSlider.on('update', function (values) {
            minInputEl.value = Number(values[0]);
            maxInputEl.value = Number(values[1]);
        });

        minInputEl.addEventListener('change', function () {
            rangeSlider.set([this.value, null]);
        });
        maxInputEl.addEventListener('change', function () {
            rangeSlider.set([null, this.value]);
        });
    });

    // counter
    $('.counter .counter--increment').on('click', function () {
        var $input = $(this).parent().find('.counter--value');
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false;
    });
    $('.counter .counter--decrement').on('click', function () {
        var $input = $(this).parent().find('.counter--value');
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false;
    });

    // general toggle function
    $('.toggler').on('click', function () {
        const target = $(this).data('target');
        $(target).toggleClass('hidden flex');
        $(this).toggleClass('active');
    });

    // toggle mobile search popup
    $('.toggle-mobile-search').on('click', function () {
        $('.mobile-search-box').toggleClass('active');
        $(this).find('.fa-regular').toggleClass('fa-search fa-times');
    });

    // paginationForm
    $('.paginationForm').on('change', function () {
        $(this).closest('form').submit();
    });

    // filter select
    $('.filterSelect').on('change', function () {
        $(this).closest('form').submit();
    });

    $('.jodit-editor').each(function (idx, item) {
        const editor = Jodit.make(item, {
            height: 300,
            width: '100%',
            uploader: {
                insertImageAsBase64URI: true,
            },
        });
        editor.setEditorValue($(item).val());
    });
});
