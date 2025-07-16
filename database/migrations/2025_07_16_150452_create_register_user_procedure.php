<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateRegisterUserProcedure extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            DROP PROCEDURE IF EXISTS RegisterUser;

            CREATE PROCEDURE RegisterUser (
                IN p_username VARCHAR(50) COLLATE utf8mb4_unicode_ci,
                IN p_nama     VARCHAR(50) COLLATE utf8mb4_unicode_ci,
                IN p_no_hp    VARCHAR(15) COLLATE utf8mb4_unicode_ci,
                IN p_email    VARCHAR(50) COLLATE utf8mb4_unicode_ci,
                IN p_password VARCHAR(255) COLLATE utf8mb4_unicode_ci
            )
            BEGIN
                DECLARE new_id VARCHAR(50);

                IF EXISTS (
                    SELECT 1 FROM tb_users
                    WHERE email COLLATE utf8mb4_unicode_ci = p_email COLLATE utf8mb4_unicode_ci
                ) THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Email sudah terdaftar";

                ELSEIF EXISTS (
                    SELECT 1 FROM tb_users
                    WHERE username COLLATE utf8mb4_unicode_ci = p_username COLLATE utf8mb4_unicode_ci
                ) THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Username sudah digunakan";

                ELSE
                    SELECT CONCAT("U", LPAD(IFNULL(SUBSTRING(MAX(id_user), 2), 0) + 1, 3, "0"))
                    INTO new_id
                    FROM tb_users
                    WHERE id_user REGEXP "^U[0-9]+$";

                    INSERT INTO tb_users (
                        id_user,
                        username,
                        nama,
                        no_hp,
                        email,
                        password,
                        created_at,
                        updated_at
                    ) VALUES (
                        new_id,
                        p_username,
                        p_nama,
                        p_no_hp,
                        p_email,
                        p_password,
                        NOW(),
                        NOW()
                    );
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS RegisterUser');
    }
}
