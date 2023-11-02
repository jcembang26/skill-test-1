(function ($) {
  'use strict';
  Drupal.behaviors.aliasHierarchyFieldsetSummaries = {
    attach: function (context) {
      $(context).find('.alias-hierarchy-form').drupalSetSummary(function (context) {
        var customAlias = $('.js-form-item-alias-hierarchy-custom-alias-0-value input', context).val();

        if (customAlias) {
          return Drupal.t('Custom alias: @alias', { '@alias': customAlias });
        }
        else {
          return Drupal.t('No custom alias');
        }
      });
    }
  };
})(jQuery);
