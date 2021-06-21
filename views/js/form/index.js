
import TranslatableInput from '@components/translatable-input';

const $ = window.$;

$(() => {

  new TranslatableInput({localeInputSelector: '.js-locale-input'});
console.log( TranslatableInput );
  // TinyMCE
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TinyMCEEditor',
    'TranslatableInput',
    'EventEmitter',
    'TextWithLengthCounter',
  ]);

});
