document.addEventListener('DOMContentLoaded', () => {

    let mainFeaturesRow = `
    <div class="woo_extndr_product_main_features_row d-flex gap-1">
                    <input type="text" name="woo_extndr_product_main_feature[][title]" class="short">
                    <input type="text" name="woo_extndr_product_main_feature[][value]" class="short">
                    <button type="button" class="button woo_extndr_feature_delete_btn">
                        Delete
                    </button>
                </div>
        `;

    let featuresContainer = document.getElementsByClassName('woo_extndr_product_main_features_container')[0];
    let featureAddBtn = document.getElementById('woo_extndr_feature_add_btn');


    featureAddBtn.addEventListener('click', () => {
        featureAddBtn.insertAdjacentHTML('beforebegin', mainFeaturesRow);
        toggleAddBtn();
        updateRowsIndices();
    });


    featuresContainer.addEventListener('click', (e) => {

        if (e.target.classList.contains('woo_extndr_feature_delete_btn')) {
            e.target.closest('.woo_extndr_product_main_features_row').remove();

            toggleAddBtn();
            updateRowsIndices();
        };

    })

    function toggleAddBtn() {
        let featuresRow = document.querySelectorAll('.woo_extndr_product_main_features_row').length;
        if (featuresRow >= 5) {
            featureAddBtn.remove();
        } else {
            featuresContainer.append(featureAddBtn);
        }
    }

    function updateRowsIndices() {
        var featuresRow = Array.from(document.getElementsByClassName('woo_extndr_product_main_features_row'));

        featuresRow.forEach((row, index) => {
            var inputTitle = row.querySelector('input[name*="[title]"]');
            var inputValue = row.querySelector('input[name*="[value]"]');

            if (inputTitle) inputTitle.name = `woo_extndr_product_main_feature[${index}][title]`;
            if (inputValue) inputValue.name = `woo_extndr_product_main_feature[${index}][value]`;
        })
    }

    toggleAddBtn();

});