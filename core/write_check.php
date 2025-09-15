<?php
// load_editor.php
function load_editor($board) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $user_role = $_SESSION['role'] ?? 0;

    // 권한 체크
    if ($user_role < $board['auth_write']) {
        return '<p>❌ 글쓰기 권한이 없습니다.</p>';
    }


    
    $editor_html = '<textarea id="content" name="content" rows="10" style="width:100%"></textarea>';

// CKEditor CDN 불러오기
$editor_html .= '<script src="https://cdn.ckeditor.com/ckeditor5/46.1.1/ckeditor5.umd.js" crossorigin></script>
<script src="https://cdn.ckeditor.com/ckeditor5-premium-features/46.1.1/ckeditor5-premium-features.umd.js" crossorigin></script>
<script src="https://cdn.ckeditor.com/ckeditor5/46.1.1/translations/ko.umd.js" crossorigin></script>
<script src="https://cdn.ckeditor.com/ckeditor5-premium-features/46.1.1/translations/ko.umd.js" crossorigin></script>
<script src="https://cdn.ckbox.io/ckbox/2.6.1/ckbox.js" crossorigin></script>'.PHP_EOL;

// CKEditor 초기화
$editor_html .= "
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof ClassicEditor !== 'undefined') {
        ClassicEditor
            .create(document.querySelector('#content'), {
                language: 'ko', // 한글
                toolbar: [
                    'heading','|',
                    'bold','italic','underline','strikethrough','link','blockQuote','codeBlock','|',
                    'bulletedList','numberedList','todoList','|',
                    'insertTable','mediaEmbed','imageUpload','|',
                    'undo','redo','fontColor','fontBackgroundColor','fontSize','fontFamily'
                ],
                ckbox: {
                    tokenUrl: '/ckbox-token-endpoint' // 필요 없으면 제거 가능
                }
            })
            .then(editor => window.editor = editor)
            .catch(error => console.error(error));
    } else {
        console.error('CKEditor 5가 로드되지 않았습니다.');
    }
});
</script>
";


    return $editor_html;
}