CKEDITOR.plugins.add('imageslider', {
  icons: this.path + 'imageslider.png',
  init: function (editor) {
    editor.addCommand('insertImageSlider', new CKEDITOR.dialogCommand('imagesliderDialog'));
    editor.ui.addButton('ImageSlider', {
      label: 'Insert Image Slider',
      command: 'insertImageSlider',
      toolbar: 'insert'
    });
    CKEDITOR.dialog.add('imagesliderDialog', this.path + 'dialog/imageslider.js');
  }
});
