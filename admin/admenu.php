<?php
$menu = [];

// 환경설정
$menu['100'] = [
    ['100000', '환경설정', '/admin/config', 'config'],
    ['100100', '사이트 설정', '/admin/config/site', 'cf_basic'],
    ['100200', '메인페이지 관리', '/admin/config/main', 'cf_main'],
    ['100300', '메뉴 관리', '/admin/config/menu', 'cf_menu'],
    ['100400', '디자인 설정', '/admin/config/design', 'cf_design'],
];

// 게시판 관리
$menu['200'] = [
    ['200000', '게시판 관리', '/admin/board', 'board'],
    ['200200', '게시판 그룹 관리', '/admin/board/groups', 'board_group'],
];

// 커뮤니티 기능
$menu['300'] = [
    ['300000', '커뮤니티', '/admin/community', 'community'],
    ['300100', 'DM 관리', '/admin/community/dm', 'dm'],
    ['300200', '상점 관리', '/admin/community/shop', 'shop'],
    ['300300', '포인트 관리', '/admin/community/points', 'points'],
    ['300400', '캐릭터 관리', '/admin/community/characters', 'characters'],
    ['300500', '신청서 관리', '/admin/community/forms', 'forms'],
];

// 유저 관리
$menu['400'] = [
    ['400000', '유저 관리', '/admin/users', 'users'],
    ['400200', '활동 내역', '/admin/users/activity', 'user_activity'],
];

// 파일/업로드 관리
$menu['500'] = [
    ['500000', '파일/업로드 관리', '/admin/files', 'files'],
    ['500100', '첨부파일 관리', '/admin/files/uploads', 'file_uploads'],
];