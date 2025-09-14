-- -------------------------------------------

-- 유저 테이블
CREATE TABLE IF NOT EXISTS `maru_users` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 메인 화면 모듈
CREATE TABLE IF NOT EXISTS `maru_main_module` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            width INT NOT NULL,
            height INT NOT NULL,
            x INT NOT NULL,
            y INT NOT NULL,
            type VARCHAR(50),
            con1 TEXT, con2 TEXT, con3 TEXT, con4 TEXT, con5 TEXT, con6 TEXT,
            con7 TEXT, con8 TEXT, con9 TEXT, con10 TEXT, con11 TEXT,
            con12 TEXT, con13 TEXT, con14 TEXT, con15 TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 그리드 삽입
INSERT INTO `maru_main_module` (id, name, width, height, x, y)
        VALUES (1, 'grid_setting', 50, 50, 0, 0)
        ON DUPLICATE KEY UPDATE width=VALUES(width), height=VALUES(height);



-- 사이트 기본 설정
CREATE TABLE IF NOT EXISTS `maru_site_setting` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            is_public TINYINT(1) NOT NULL DEFAULT 0,
            allow_account_create TINYINT(1) NOT NULL DEFAULT 0,
            allow_character_create TINYINT(1) NOT NULL DEFAULT 0,
            allow_character_edit TINYINT(1) NOT NULL DEFAULT 0,
            site_title VARCHAR(255) NOT NULL,
            site_description TEXT,
            favicon VARCHAR(255) DEFAULT NULL,
            main_image VARCHAR(255) DEFAULT NULL,
            bgm VARCHAR(255) DEFAULT NULL,
            twitter_widget TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 기본 설정 삽입
INSERT INTO maru_site_setting (id, is_public, site_title, site_description) 
        VALUES (1, 1, '내 홈페이지', '사이트 설명입니다.')
        ON DUPLICATE KEY UPDATE id=1;


-- 메뉴 테이블
CREATE TABLE IF NOT EXISTS `maru_menu` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            icon_img VARCHAR(255) DEFAULT NULL,
            link VARCHAR(255) DEFAULT NULL,
            target VARCHAR(20) DEFAULT NULL,
            order_num INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 게시판 그룹 테이블
CREATE TABLE IF NOT EXISTS `maru_board_group` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            table_id VARCHAR(255) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            auth_role TINYINT(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 기본 게시판 그룹 삽입
INSERT INTO maru_board_group (id, table_id, name, auth_role) 
        VALUES (1, 'home', 'HOME', '0')
        ON DUPLICATE KEY UPDATE id=1;


-- 게시판 테이블
CREATE TABLE IF NOT EXISTS `maru_board` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            table_id VARCHAR(255) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            group_id varchar(255) NOT NULL,
            category VARCHAR(255) DEFAULT NULL,
            use_category TINYINT(1) NOT NULL DEFAULT 0,
            skin VARCHAR(255) DEFAULT 'basic',
            password VARCHAR(255) DEFAULT NULL,

            auth_list TINYINT(1) NOT NULL DEFAULT 0,
            auth_read TINYINT(1) NOT NULL DEFAULT 0,
            auth_write TINYINT(1) NOT NULL DEFAULT 0,
            auth_comment TINYINT(1) NOT NULL DEFAULT 0,
            modify_level INT NOT NULL DEFAULT 1,
            delete_level INT NOT NULL DEFAULT 1,
            secret_level TINYINT(1) NOT NULL DEFAULT 0,
            use_noname TINYINT(1) NOT NULL DEFAULT 0,
            file_count INT NOT NULL DEFAULT 1,
            file_size INT NOT NULL DEFAULT 512,
            use_html_editor TINYINT(1) NOT NULL DEFAULT 0,
            txt_min INT NOT NULL DEFAULT 0,
            txt_max INT NOT NULL DEFAULT 0,
            comment_min INT NOT NULL DEFAULT 0,
            comment_max INT NOT NULL DEFAULT 0,

            content_top TEXT,
            insert_content TEXT,

            page_row INT NOT NULL DEFAULT 10,
            image_width INT NOT NULL DEFAULT 600,
            new_icon_hour INT NOT NULL DEFAULT 24,
            reply_order TINYINT(1) NOT NULL DEFAULT 0,
            list_order TINYINT(1) NOT NULL DEFAULT 0,
            read_point INT NOT NULL DEFAULT 0,
            write_point INT NOT NULL DEFAULT 0,
            comment_point INT NOT NULL DEFAULT 0,

            con_txt_1 VARCHAR(255) DEFAULT NULL, con_txt_2 VARCHAR(255) DEFAULT NULL, con_txt_3 VARCHAR(255) DEFAULT NULL,
            con_txt_4 VARCHAR(255) DEFAULT NULL,con_txt_5 VARCHAR(255) DEFAULT NULL,con_txt_6 VARCHAR(255) DEFAULT NULL,
            con_txt_7 VARCHAR(255) DEFAULT NULL,con_txt_8 VARCHAR(255) DEFAULT NULL,con_txt_9 VARCHAR(255) DEFAULT NULL,
            con_txt_10 VARCHAR(255) DEFAULT NULL,
            con_1 varchar(255) DEFAULT NULL, con_2 varchar(255) DEFAULT NULL, con_3 varchar(255) DEFAULT NULL,
            con_4 varchar(255) DEFAULT NULL, con_5 varchar(255) DEFAULT NULL, con_6 varchar(255) DEFAULT NULL,
            con_7 varchar(255) DEFAULT NULL, con_8 varchar(255) DEFAULT NULL, con_9 varchar(255) DEFAULT NULL,
            con_10 varchar(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 게시글 테이블
CREATE TABLE IF NOT EXISTS `maru_board_post` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            board_id VARCHAR(255) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            content TEXT not null,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            author_id INT,
            view_count INT UNSIGNED DEFAULT 0,
            like_count INT UNSIGNED DEFAULT 0,
            comment_count INT UNSIGNED DEFAULT 0,
            is_notice TINYINT(1) DEFAULT 0,
            status TINYINT(1) DEFAULT 1,
            ip_address VARBINARY(16) DEFAULT NULL,
            is_secret TINYINT(1) DEFAULT 0,
            password VARCHAR(255) DEFAULT NULL,
            category VARCHAR(255) DEFAULT NULL,
            is_noname TINYINT(1) DEFAULT 0,

            con_1 varchar(255) DEFAULT NULL, con_2 varchar(255) DEFAULT NULL, con_3 varchar(255) DEFAULT NULL,
            con_4 varchar(255) DEFAULT NULL, con_5 varchar(255) DEFAULT NULL, con_6 varchar(255) DEFAULT NULL,
            con_7 varchar(255) DEFAULT NULL, con_8 varchar(255) DEFAULT NULL, con_9 varchar(255) DEFAULT NULL,
            con_10 varchar(255) DEFAULT NULL, con_11 varchar(255) DEFAULT NULL, con_12 varchar(255) DEFAULT NULL,

            INDEX(board_id, created_at DESC),
            INDEX(author_id, created_at DESC),
            INDEX(board_id, is_notice, created_at DESC)

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 댓글 테이블
CREATE TABLE IF NOT EXISTS `maru_board_comment` (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            post_id BIGINT NOT NULL,
            user_id INT NOT NULL,
            parent_comment_id BIGINT NULL,
            is_anonymous TINYINT(1) DEFAULT 0,
            content TEXT NOT NULL,
            status TINYINT(1) DEFAULT 1,
            ip_address VARBINARY(16) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP 
                        ON UPDATE CURRENT_TIMESTAMP,

            INDEX(post_id, created_at),
            INDEX(user_id, created_at),
            INDEX(parent_comment_id)

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;