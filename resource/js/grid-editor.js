const grid = document.getElementById('grid');
const gridSizeInput = document.getElementById('gridSize');
const addModuleBtn = document.getElementById('addModuleBtn');
const saveBtn = document.getElementById('saveBtn');

let gridSize = parseInt(gridSizeInput.value);

gridSizeInput.addEventListener('change', () => {
  gridSize = parseInt(gridSizeInput.value);
  grid.style.backgroundSize = `${gridSize}px ${gridSize}px`;

  // 기존 모듈 위치/크기 재조정
  const modules = document.querySelectorAll('.module');
  modules.forEach(mod => {
    let x = Math.round(parseInt(mod.style.left) / gridSize) * gridSize;
    let y = Math.round(parseInt(mod.style.top) / gridSize) * gridSize;
    let w = Math.round(parseInt(mod.offsetWidth) / gridSize) * gridSize;
    let h = Math.round(parseInt(mod.offsetHeight) / gridSize) * gridSize;

    mod.style.left = x + 'px';
    mod.style.top = y + 'px';
    mod.style.width = w + 'px';
    mod.style.height = h + 'px';
  });
  saveLayout();
});

addModuleBtn.addEventListener('click', () => {
  createModule(
    0,                   // x 좌표
    0,                   // y 좌표
    gridSize * 2,        // width
    gridSize * 2,        // height
    `new-Module` // name
  );
  saveLayout();
});

saveBtn.addEventListener('click',() => {
  saveLayout();
  updateModuleList();});

// 🔹 레이아웃 저장 함수
function saveLayout() {
  const modules = [];
  document.querySelectorAll('.module').forEach(mod => {
    // 닫기 버튼 문자 제거
    modules.push({
      name: mod.innerText.slice(0, -1).trim(), // × 제거
      x: parseInt(mod.style.left),
      y: parseInt(mod.style.top),
      width: parseInt(mod.offsetWidth/gridSize)*gridSize,
      height: parseInt(mod.offsetHeight/gridSize)*gridSize
    });
  });

  const data = {
    gridSize: gridSize,  // 그리드 크기 저장
    modules: modules
  };

  fetch('/adm/detail/save_layout.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'layout_json=' + encodeURIComponent(JSON.stringify(data))
  })
    .then(res => res.json())
}


window.addEventListener('DOMContentLoaded', () => {
  // 페이지 로드 시 layout.json 불러오기
  fetch('/adm/detail/layout.json')
    .then(res => {
      if (!res.ok) throw new Error("layout.json 없음");
      return res.json();
    })
    .then(data => {
      // 🔹 그리드 크기 로드
      if (data.gridSize) {
        gridSize = data.gridSize;
        gridSizeInput.value = gridSize;
        grid.style.backgroundSize = `${gridSize}px ${gridSize}px`;
      }

      // 🔹 모듈 로드
      if (data.modules) {
        data.modules.forEach(mod => {
          createModule(mod.x, mod.y, mod.width, mod.height, mod.name);
        });
      }
    })
    .catch(() => {
      console.log("저장된 레이아웃 없음, 새로 시작합니다.");
    });
});

function createModule(x, y, w, h, name = 'Module') {
  const mod = document.createElement('div');
  mod.classList.add('module');
  mod.style.left = x + 'px';
  mod.style.top = y + 'px';
  mod.style.width = w + 'px';
  mod.style.height = h + 'px';
  mod.innerText = name;

  // 닫기 버튼
  const closeBtn = document.createElement('span');
  closeBtn.innerText = '×';
  closeBtn.style.position = 'absolute';
  closeBtn.style.top = '2px';
  closeBtn.style.right = '5px';
  closeBtn.style.cursor = 'pointer';
  closeBtn.addEventListener('click', () =>{ 
    mod.remove();
    saveLayout();
  });
  mod.appendChild(closeBtn);

  // 리사이즈 핸들
  const resizeHandle = document.createElement('div');
  resizeHandle.style.width = '10px';
  resizeHandle.style.height = '10px';
  resizeHandle.style.background = 'black';
  resizeHandle.style.position = 'absolute';
  resizeHandle.style.right = '0';
  resizeHandle.style.bottom = '0';
  resizeHandle.style.cursor = 'se-resize';
  mod.appendChild(resizeHandle);

  grid.appendChild(mod);

  // 드래그 기능
  mod.onmousedown = function (e) {
    if (e.target === resizeHandle || e.target === closeBtn) return; // 핸들이나 닫기 버튼 클릭 제외
    
    const gridRect = grid.getBoundingClientRect();
    const modRect = mod.getBoundingClientRect();

    // 마우스 클릭 위치와 모듈 좌상단 차이
    const shiftX = e.clientX - modRect.left;
    const shiftY = e.clientY - modRect.top;

    function moveAt(clientX, clientY) {
      // 그리드 컨테이너 기준 좌표 계산
      let newX = clientX - gridRect.left - shiftX;
      let newY = clientY - gridRect.top - shiftY;

      // 그리드 단위 스냅
      newX = Math.round(newX / gridSize) * gridSize;
      newY = Math.round(newY / gridSize) * gridSize;

      // 그리드 영역 제한
      newX = Math.max(0, Math.min(newX, grid.offsetWidth - mod.offsetWidth));
      newY = Math.max(0, Math.min(newY, grid.offsetHeight - mod.offsetHeight));

      mod.style.left = newX + 'px';
      mod.style.top = newY + 'px';
    }

    function onMouseMove(e) {
      moveAt(e.clientX, e.clientY);
    }

    document.addEventListener('mousemove', onMouseMove);
    document.onmouseup = function () {
      document.removeEventListener('mousemove', onMouseMove);
      document.onmouseup = null;
      saveLayout();
    };
  };

  // 리사이즈 기능
  resizeHandle.onmousedown = function (e) {
    e.stopPropagation();
    let startX = e.clientX;
    let startY = e.clientY;
    let startWidth = mod.offsetWidth;
    let startHeight = mod.offsetHeight;

    function onMouseMove(e) {
      let newWidth = startWidth + (e.clientX - startX);
      let newHeight = startHeight + (e.clientY - startY);

      // 🔹 그리드 단위 반올림
      newWidth = Math.round(newWidth / gridSize) * gridSize;
      newHeight = Math.round(newHeight / gridSize) * gridSize;

      // 🔹 최소 크기 제한
      newWidth = Math.max(gridSize, newWidth);
      newHeight = Math.max(gridSize, newHeight);

      // 🔹 최대 크기 제한 (그리드 영역 안)
      newWidth = Math.min(newWidth, grid.offsetWidth - parseInt(mod.style.left));
      newHeight = Math.min(newHeight, grid.offsetHeight - parseInt(mod.style.top));

      mod.style.width = newWidth + 'px';
      mod.style.height = newHeight + 'px';
    }

    document.addEventListener('mousemove', onMouseMove);
    document.onmouseup = function () {
      document.removeEventListener('mousemove', onMouseMove);
      document.onmouseup = null;
      saveLayout();
    };
  };

  return mod;
}
document.addEventListener('DOMContentLoaded', () => {
  const moduleList = document.getElementById('moduleTable');

  function updateModuleList() {
    fetch('/adm/detail/layout.json')
      .then(res => {
        if (!res.ok) throw new Error("layout.json 없음");
        return res.json();
      })
      .then(data => {
        moduleList.innerHTML = ''; // 기존 목록 초기화
        
        if (data.modules) {
          data.modules.forEach(mod => {
            const tr = document.createElement('tr');

            const tdName = document.createElement('td');
            tdName.innerText = mod.name;
            tr.appendChild(tdName);
          });
        }
      })
      .catch(err => {
        console.log("저장된 레이아웃 없음, 새로 시작합니다.", err);
      });
  };
  window.updateModuleList = updateModuleList; // 전역으로 노출
});

