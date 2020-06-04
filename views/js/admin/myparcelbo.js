document.addEventListener('DOMContentLoaded', function() {

  const insuranceCheckboxSelector = '#myparcel-insurance-checkbox';
  let toggleInsuranceAdditional = function() {
    const insuranceAdditionalActiveClassname = 'insurance-active';
    let $insuranceCheckbox = $(insuranceCheckboxSelector);
    let $insuranceAdditional = $('.insurance-additional-container');
    let isChecked = $insuranceCheckbox.is(':checked');

    if(isChecked) {
      $insuranceAdditional.addClass(insuranceAdditionalActiveClassname);
    } else {
      $insuranceAdditional.removeClass(insuranceAdditionalActiveClassname);
    }
  };
  $(document).on('change', insuranceCheckboxSelector, function() {
    toggleInsuranceAdditional();
  });
  toggleInsuranceAdditional();

  const insuranceHigherAmountSelector = '#myparcel-insurance-higher-amount';
  let changeInsuranceHigherAmount = function() {
    const amountStep = 500;
    let $insuranceHigherAmountInput = $(insuranceHigherAmountSelector);
    let currentValue = parseFloat($insuranceHigherAmountInput.val());

    let steps = Math.ceil(currentValue / amountStep);
    let newValue = amountStep * steps;
    if (newValue > 5000) {
      newValue = 5000;
    }
    $insuranceHigherAmountInput.val(newValue);
  };
  $(document).on('change', insuranceHigherAmountSelector, function() {
    changeInsuranceHigherAmount();
  });
  changeInsuranceHigherAmount();
  if (typeof prompt_for_label_position !== 'undefined') {
    if (parseInt(prompt_for_label_position) === 1) {
      $('#a6, #a6_bulk').change(function () {
        $('.positions-block').hide();
      });
      $('#a4, #a4_bulk').change(function () {
        $('.positions-block').show();
      });
      if ($('#a6').is(':checked') || $('#a6_bulk').is(':checked')) {
        $('.positions-block').hide();
      }
    } else {
      $('.positions-block').remove();
    }
  }

  if ($('#MY_PARCEL_LABEL_SIZE').val() == 'a6') {
    $('.label_position').hide();
  }
  $('#MY_PARCEL_LABEL_SIZE').change(function() {
    if ($(this).val() == 'a6') {
      $('.label_position').hide();
    } else {
      $('.label_position').show();
    }
  });
  $(document).on('click', '.label-description-variables code', function() {
    var $input = $(this).closest('.col-lg-9').find('input[type="text"]');
    if ($input.length) {
      $input.val($input.val() + ' ' + $(this).html());
    }
  });
}, false);
