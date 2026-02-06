document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('pcafe_spf_woo_configuration_type');
    const allInputs = document.querySelectorAll('.pcafe_spf_geoip_field, .pcafe_spf_default_country_field, .pcafe_spf_validation_field');

    function toggleFields() {
        const selectedValue = select.value;

        allInputs.forEach(input => {
            const parentTr = input.closest('tr');
            if (!parentTr) return;

            if (selectedValue === 'global') {
                parentTr.style.display = 'none'; // hide row
            } else if (selectedValue === 'custom') {
                parentTr.style.display = ''; // show row
            }
        });
    }

    // Run on page load
    toggleFields();

    // Run on select change
    select.addEventListener('change', toggleFields);
});