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
                <div class="module" data-id="${m.id}" style="width:${m.width}px; height:${m.height}px; left:${m.x}px; top:${m.y}px;">
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
                grid:[GRID_SIZE, GRID_SIZE],
                containment: "parent",
                stop:function(){ savePosition($(this)); }
            });

            // 리사이즈
            $mod.resizable({
                grid: GRID_SIZE,
                containment: "parent",
                stop: function(event, ui){
                  // width/height를 GRID_SIZE 배수로 맞춤
                  let w = Math.round($mod.width() / GRID_SIZE) * GRID_SIZE;
                  let h = Math.round($mod.height() / GRID_SIZE) * GRID_SIZE;
                  $mod.width(w);
                  $mod.height(h);
                  savePosition($mod);
              }
            });

            // 삭제
            $mod.find('.btn-delete').click(function(){
                if(confirm("삭제하시겠습니까?")){
                    $.post('/adm/detail/save.php', {action:'delete', id:m.id}, function(){
                        MODULES = MODULES.filter(x=>x.id!=m.id);
                        renderModules();
                    });
                }
            });

            // 설정 버튼
            $mod.find('.btn-settings').click(function(){
                alert("설정 모달 열기");
            });
        });
    }

    renderModules();

    function savePosition($mod){
        const id = $mod.data('id');
        const pos = $mod.position();
        $.post('/adm/detail/save.php', {
            action:'update',
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
            $.post('/adm/detail/save.php', {action:'update_grid', size:newSize}, function(){
                GRID_SIZE = newSize;
                renderGridBackground();
                renderModules();
            });
        }
    });

    // 모듈 추가
    $('#addModule').click(function(){
        $.post('/adm/detail/save.php', {action:'add', name:'새 모듈'}, function(data){
            const newModule = JSON.parse(data);
            MODULES.push(newModule);
            renderModules();
        });
    });
});
