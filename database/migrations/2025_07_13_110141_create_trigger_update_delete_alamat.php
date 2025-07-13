<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Trigger AFTER UPDATE
        DB::unprepared('
            CREATE TRIGGER trg_after_update_alamat
            AFTER UPDATE ON tb_alamat
            FOR EACH ROW
            BEGIN
                DECLARE alamat_utama_count INT;

                SELECT COUNT(*) INTO alamat_utama_count
                FROM alamat
                WHERE id_user = NEW.id_user AND status = "utama";

                IF alamat_utama_count = 0 THEN
                    UPDATE alamat
                    SET status = "utama"
                    WHERE id_alamat = NEW.id_alamat;
                END IF;
            END
        ');

        // Trigger AFTER DELETE
        DB::unprepared('
            CREATE TRIGGER trg_after_delete_alamat
            AFTER DELETE ON tb_alamat
            FOR EACH ROW
            BEGIN
                DECLARE alamat_utama_count INT;
                DECLARE last_id_alamat INT;

                SELECT COUNT(*) INTO alamat_utama_count
                FROM alamat
                WHERE id_user = OLD.id_user AND status = "utama";

                IF alamat_utama_count = 0 THEN
                    SELECT id_alamat INTO last_id_alamat
                    FROM alamat
                    WHERE id_user = OLD.id_user
                    ORDER BY updated_at DESC
                    LIMIT 1;

                    UPDATE alamat
                    SET status = "utama"
                    WHERE id_alamat = last_id_alamat;
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_update_alamat');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_delete_alamat');
    }
};


