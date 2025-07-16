<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            DROP TRIGGER IF EXISTS kurangi_stok_produk_per_toko;

            CREATE TRIGGER kurangi_stok_produk_per_toko
            AFTER INSERT ON tb_detail_transaksi
            FOR EACH ROW
            BEGIN
                DECLARE v_id_toko VARCHAR(10);
                DECLARE v_stok_sekarang INT;

                -- Ambil id_toko dari tb_transaksi
                SELECT id_toko INTO v_id_toko
                FROM tb_transaksi
                WHERE id_transaksi COLLATE utf8mb4_general_ci = NEW.id_transaksi COLLATE utf8mb4_general_ci;

                -- Ambil stok dari tb_produk_toko
                SELECT stok INTO v_stok_sekarang
                FROM tb_produk_toko
                WHERE id_toko COLLATE utf8mb4_general_ci = v_id_toko COLLATE utf8mb4_general_ci
                  AND id_produk COLLATE utf8mb4_general_ci = NEW.id_produk COLLATE utf8mb4_general_ci;

                -- Update stok jika cukup
                IF v_stok_sekarang >= NEW.jumlah THEN
                    UPDATE tb_produk_toko
                    SET 
                        stok = v_stok_sekarang - NEW.jumlah,
                        status = IF(v_stok_sekarang - NEW.jumlah <= 0, "habis", "tersedia"),
                        updated_at = NOW()
                    WHERE id_toko COLLATE utf8mb4_general_ci = v_id_toko COLLATE utf8mb4_general_ci
                      AND id_produk COLLATE utf8mb4_general_ci = NEW.id_produk COLLATE utf8mb4_general_ci;
                ELSE
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Stok toko tidak mencukupi";
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS kurangi_stok_produk_per_toko');
    }
};

