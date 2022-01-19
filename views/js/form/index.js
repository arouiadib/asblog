
import TranslatableInput from '@components/translatable-input';
import ChoiceTree from '@components/form/choice-tree';
import ChoiceTable from '@components/choice-table';

const $ = window.$;

$(() => {
  new TranslatableInput({localeInputSelector: '.js-locale-input'});

  // TinyMCE
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TinyMCEEditor',
    'TranslatableInput',
    'EventEmitter',
    'TextWithLengthCounter',
  ]);

  $('.datetimepicker').datetimepicker({
    locale: 'es',
    useCurrent: false,
    sideBySide: true
  });

  // Choice tree for category form
  new ChoiceTree('#form_category_id_parent');
  new ChoiceTree('#form_category_shop_association').enableAutoCheckChildren();

  // Choice table for categories in post form
  new ChoiceTable();
});

