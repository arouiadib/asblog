const {$} = window;

$(() => {
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TinyMCEEditor',
    'TranslatableInput',
   // 'EventEmitter',
    //'TextWithLengthCounter',
  ]);
});
