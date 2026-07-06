jQuery(document).ready(function ($) {

    const $variationAction = $('.woo-extender-variation-select').data('action');

    $('.woo-extender-parent-product-search').each(function () {
        const $el = $(this);
        const $searchAction = $el.data('action');
        const $variationSelect = $el.closest('form').find('.woo-extender-variation-select');

        function resetVariationSelect() {
            $variationSelect.html('<option value="">-- انتخاب ورییشن (اختیاری) --</option>').prop('disabled', true);
            console.log("فیلد متغییرها ریست شد.");
        };

        function loadVariations(productId) {
            if (!productId) {
                resetVariationSelect();
                return;
            }

            $variationSelect.html('<option value="">در حال دریافت متغییرها...</option>').prop('disabled', true);

            $.ajax({
                url: woo_extender_admin_batches.ajax_url,
                type: 'GET',
                dataType: 'json',
                data: {
                    action: $variationAction,
                    product_id: productId,
                    security: woo_extender_admin_batches.nonce
                },
                success: function (response) {
                    console.log("پاسخ دریافتی از سرور:", response);

                    if (response.success && response.data && response.data.length > 0) {
                        var options = '<option value="">-- انتخاب متغیر (اختیاری) --</option>';

                        $.each(response.data, function (index, variation) {
                            options += '<option value="' + variation.id + '">' + variation.text + '</option>';
                        });

                        $variationSelect.html(options).prop('disabled', false);
                        console.log("منوی متغییرها با موفقیت به‌روزرسانی و فعال شد.");
                    } else {
                        $variationSelect.html('<option value="">این محصول ورییشن ندارد</option>').prop('disabled', true);
                        console.log("محصول فاقد ورییشن فعال است.");
                    }
                },
                error: function () {
                    $variationSelect.html('<option value="">خطا در ارتباط با سرور</option>').prop('disabled', true);
                    console.log("خطا در برقراری ارتباط با فایل PHP رخ داد.");
                }
            });
        };

        $el.selectWoo({
            placeholder: $el.data('placeholder'),
            allowClear: false,
            width: 'resolve',
            minimumInputLength: 3,
            ajax: {
                url: woo_extender_admin_batches.ajax_url,
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term,
                        action: $searchAction,
                        security: woo_extender_admin_batches.nonce
                    };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });

        $el.on('change', function () {
            var productId = $(this).val();
            loadVariations(productId);
        });

        var initialParentId = $el.val();
        var hasSavedVariation = $variationSelect.data('selected');

        if (initialParentId && !hasSavedVariation) {
            console.log("محصول پیش‌فرض شناسایی شد؛ لود خودکار متغییرها...", initialParentId);
            loadVariations(initialParentId);
        } else if (!initialParentId) {

            resetVariationSelect();
        }
    });

    $('.woo-extender-warranty-provider-id-select').each(function () {
        const $el = $(this);
        const $action = $el.data('action');

        $el.selectWoo({
            placeholder: $el.data('placeholder'),
            allowClear: true,
            width: 'resolve',
            minimumInputLength: 3,
            ajax: {
                url: woo_extender_admin_batches.ajax_url,
                dataType: 'json',
                delay: 300,
                cache: true,
                data: function (params) {
                    return {
                        q: params.term,
                        action: $action,
                        security: woo_extender_admin_batches.nonce
                    };
                },
                processResults: function (data) {
                    console.log(data);
                    return { results: data };
                }
            }
        })
    });

    $('.woo-extender-supplier-id-select').each(function () {
        const $el = $(this);
        const $action = $el.data('action');

        $el.selectWoo({
            placeholder: $el.data('placeholder'),
            allowClear: true,
            width: 'resolve',
            minimumInputLength: 3,
            ajax: {
                url: woo_extender_admin_batches.ajax_url,
                dataType: 'json',
                delay: 300,
                cache: true,
                data: function (params) {
                    return {
                        q: params.term,
                        action: $action,
                        security: woo_extender_admin_batches.nonce
                    };
                },
                processResults: function (data) {
                    console.log(data);
                    return { results: data };
                }
            }
        })
    });
});