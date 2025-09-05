// 플러그인 파일 경로: /plugin/summernote-slickslider/summernote-slickslider.js

(function(factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD
    define(['jquery'], factory);
  } else if (typeof module === 'object' && module.exports) {
    // Node/CommonJS
    module.exports = factory(require('jquery'));
  } else {
    // Browser globals
    factory(window.jQuery);
  }
}(function($) {
  $.extend($.summernote.plugins, {
    'slickslider': function(context) {
      const self = this;
      const ui = $.summernote.ui;
      const editor = context.layoutInfo.editor;

      context.memo('button.slickslider', function() {
        return ui.button({
          contents: '<i class="note-icon-picture"></i> 슬라이더',
          tooltip: 'Slick 슬라이더 삽입',
          click: function () {
            self.showDialog();
          }
        }).render();
      });

      this.showDialog = function () {
        const $dialog = $(
          '<div class="modal" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog" role="document">' +
              '<div class="modal-content">' +
                '<div class="modal-header">' +
                  '<h5 class="modal-title">슬라이더 이미지 삽입</h5>' +
                  '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                  '</button>' +
                '</div>' +
                '<div class="modal-body">' +
                  '<input type="file" id="slick-upload" multiple accept="image/*" />' +
                  '<div id="slick-preview" style="margin-top:10px;"></div>' +
                '</div>' +
                '<div class="modal-footer">' +
                  '<button type="button" class="btn btn-primary" id="insert-slider">삽입</button>' +
                  '<button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>' +
                '</div>' +
              '</div>' +
            '</div>' +
          '</div>'
        );

        $dialog.appendTo(document.body).modal('show');

        const $input = $dialog.find('#slick-upload');
        const $preview = $dialog.find('#slick-preview');
        let fileList = [];

        $input.on('change', function () {
          fileList = Array.from(this.files);
          $preview.empty();
          fileList.forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
              const img = $('<img>').attr('src', e.target.result).css({width: '80px', margin: '5px'});
              $preview.append(img);
            };
            reader.readAsDataURL(file);
          });
        });

        $dialog.find('#insert-slider').on('click', function () {
          let sliderHTML = '<div class="slick-slider">';
          fileList.forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
              sliderHTML += `<div><img src="${e.target.result}" style="width:100%"></div>`;
              if (file === fileList[fileList.length - 1]) {
                sliderHTML += '</div>';
                context.invoke('editor.pasteHTML', sliderHTML);
                $dialog.modal('hide');
              }
            };
            reader.readAsDataURL(file);
          });
        });

        $dialog.on('hidden.bs.modal', function () {
          $dialog.remove();
        });
      };
    }
  });
}));
