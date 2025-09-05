CKEDITOR.dialog.add('imagesliderDialog', function (editor) {
  let selectedImages = [];

  return {
    title: '이미지 슬라이드 추가',
    minWidth: 500,
    minHeight: 400,
    contents: [
      {
        id: 'tab-basic',
        label: '슬라이드 이미지',
        elements: [
          {
            type: 'html',
            id: 'uploadBtn',
            html: `
              <input type="file" id="uploadInput" accept="image/*">
              <div id="image-list" style="max-height:180px; overflow:auto; display:flex; flex-wrap:wrap; gap:8px; margin-top:10px;"></div>
              <p style="margin-top:10px; color:gray;">이미지를 클릭하면 선택됩니다.</p>
              <div id="selected-preview" style="margin-top:10px; display:flex; flex-wrap:wrap; gap:5px;"></div>
            `
          }
        ]
      }
    ],
    onShow: function () {
      selectedImages = []; // 새로 열 때마다 초기화


      // 업로드 input에 이벤트 리스너 추가
      setTimeout(() => {
        const uploadInput = document.getElementById('uploadInput');
        if (uploadInput) {
          uploadInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('upload', file);

            fetch('/plugin/editor/ckeditor_full/imageUpload/upload.php', {
              method: 'POST',
              body: formData
            })
              .then(res => res.json())
              .then(data => {
                console.log('업로드 응답:', data);

                if (data.uploaded) {
                  // selectedImages 배열에 추가
                  if (!window.selectedImages) window.selectedImages = [];
                  window.selectedImages.push(data.url);

                  // 썸네일 이미지 추가
                  const img = document.createElement('img');
                  img.src = data.url;
                  img.style.width = '80px';
                  img.style.marginRight = '5px';
                  img.style.cursor = 'pointer';

                  // ✅ 클릭하면 선택 이미지로 추가
                  img.onclick = function () {
                    const isSelected = selectedImages.includes(img.src);

                    if (!isSelected) {
                      selectedImages.push(img.src);
                      img.style.filter = 'brightness(60%)';  // 이미지 어둡게 처리
                      img.style.border = '2px solid #007BFF'; // 테두리 강조 (선택 느낌)
                    } else {
                      // 선택 해제 기능도 추가 (선택된 이미지를 다시 클릭)
                      const index = selectedImages.indexOf(img.src);
                      if (index !== -1) {
                        selectedImages.splice(index, 1);
                      }
                      img.style.filter = '';
                      img.style.border = '';
                    }
                  };
                  document.getElementById('selected-preview').appendChild(img);
                } else {
                  alert('업로드 실패: ' + data.error?.message || '알 수 없는 오류');
                }
              })
              .catch(err => {
                console.error('업로드 에러:', err);
                alert('업로드 중 오류가 발생했습니다.');
              });
          });
        }
      }, 100); // 약간의 지연을 두고 DOM이 준비된 후 바인딩
    },
    onOk: function () {
      if (!selectedImages.length) {
    alert('슬라이드에 삽입할 이미지를 선택해주세요.');
    return;
  }

  const sliderId = 'slider-' + Math.random().toString(36).substr(2, 9);
  const html = '<div id="' + sliderId + '" class="ckeditor-slick-slider">' +
    selectedImages.map(url => '<div><img src="' + url + '" style="width:100%"></div>').join('') +
    '</div>';

  try {
    editor.focus();
    editor.insertHtml(html);
    console.log('HTML inserted into editor:', html);
  } catch (e) {
    console.error('Error inserting HTML:', e);
  }

  // Slick 슬라이더 적용
  setTimeout(() => {
    const jq = window.parent.jQuery || window.jQuery;
    if (jq && typeof jq.fn.slick === 'function') {
      jq('#' + sliderId).slick({
        arrows: true,
        dots: true
      });
      console.log('Slick initialized for:', sliderId);
    } else {
      console.warn('jQuery or slick not found.');
    }
  }, 100);
    }
  };
});
