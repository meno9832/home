$(function(){
    const $grid = $('#grid');

    function renderGridBackground(){
        $grid.css('background-size', GRID_SIZE+'px '+GRID_SIZE+'px');
    }

    renderGridBackground();

    // 모듈 렌더링
    function renderModules(){
    $grid.empty();
    MODULES.forEach(m => {
        const $mod = $(`
            <div class="module" data-id="${m.id}" 
                 style="width:${m.width}px; height:${m.height}px; left:${m.x}px; top:${m.y}px;">
                <div class="controls">
                    <button class="btn-settings">⚙️</button>
                    <button class="btn-delete">❌</button>
                </div>
                <div class="content">${m.name}</div>
            </div>
        `);

        $grid.append($mod);

        // 드래그
        $mod.draggable({
            grid: [GRID_SIZE, GRID_SIZE],
            containment: "parent",
            stop: function(){ savePosition($(this)); }
        });

        // 리사이즈
        $mod.resizable({
            grid: GRID_SIZE,
            containment: "parent",
            stop: function(){
                // width/height를 GRID_SIZE 배수로 맞춤
                let w = Math.round($mod.width() / GRID_SIZE) * GRID_SIZE;
                let h = Math.round($mod.height() / GRID_SIZE) * GRID_SIZE;
                $mod.width(w);
                $mod.height(h);
                savePosition($mod);
            }
        });
    });
}

/* --------------------------
   이벤트 위임 (한번만 실행)
--------------------------- */

// 삭제 버튼
$grid.on('click', '.btn-delete', function(){
    const $mod = $(this).closest('.module');
    const id = $mod.data('id');
    console.log("삭제 전 MODULES:", MODULES);
    if(confirm("삭제하시겠습니까?")){
        $.post('/adm/detail/save.php', {action:'delete', table: "main_module", id:id}, function(){
            MODULES = MODULES.filter(x => String(x.id) !== String(id));
            console.log("삭제 후 MODULES:", MODULES);
            renderModules(); // 즉시 반영
        });
    }
});

// 설정 버튼
$grid.on('click', '.btn-settings', function(){
    const id = $(this).closest('.module').data('id');
    alert("설정 모달 열기 (id: " + id + ")");
});

    renderModules();

    function savePosition($mod){
        const id = $mod.data('id');
        const pos = $mod.position();
        $.post('/adm/detail/save.php', {
            action:'update',
            table: "main_module",
            id:id,
            x:pos.left,
            y:pos.top,
            width:$mod.width(),
            height:$mod.height()
        });
    }

    // 그리드 크기 변경
    $('#saveGridSize').click(function(){
        const newSize = parseInt($('#gridSizeInput').val());
        if(newSize>0){
            $.post('/adm/detail/save.php', {action:'update_grid', table: "main_module", size:newSize}, function(){
                GRID_SIZE = newSize;
                renderGridBackground();
                renderModules();
            });
        }
    });

    // 모듈 추가
    $('#addModule').click(function(){
        $.post('/adm/detail/save.php', {action:'add', table: "main_module", name:'새 모듈'}, function(res){
            MODULES.push(res.data);
            renderModules();
        }, 'json');
    });
});
