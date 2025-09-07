<head>
  <meta charset="utf-8">
</head>
<div class="grid-editor">
  <div id="controls">
    그리드 크기: <input type="number" id="gridSize" value="50" min="10" style="width:60px"> px
    <button id="addModuleBtn">모듈 추가</button>
    <button id="saveBtn">저장</button>
  </div>
  <div id="grid"></div>

  <script src="<?= PATH_JS ?>/grid-editor.js"></script>
</div>
<h3>📋 현재 모듈 목록</h3>
<table >
  <thead>
    <tr>
      <th>이름</th>
      <th>X</th>
      <th>Y</th>
      <th>Width</th>
      <th>Height</th>
    </tr>
  </thead>
  <tbody id="moduleTable"></tbody>
</table>