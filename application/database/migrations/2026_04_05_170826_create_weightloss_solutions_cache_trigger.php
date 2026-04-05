<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION weightloss_solutions_cache()
            RETURNS TRIGGER AS $$
            BEGIN
                IF (TG_OP = 'INSERT' AND NEW.category_id = 5) THEN
                    INSERT INTO cache_invalidation (cache_key) VALUES ('weightloss_solutions_5');
                    RETURN NEW;
                END IF;

                IF (TG_OP = 'DELETE' AND OLD.category_id = 5) THEN
                    INSERT INTO cache_invalidation (cache_key) VALUES ('weightloss_solutions_5');
                    RETURN OLD;
                END IF;

                IF (TG_OP = 'UPDATE' AND (OLD.category_id = 5 OR NEW.category_id = 5)) THEN
                    INSERT INTO cache_invalidation (cache_key) VALUES ('weightloss_solutions_5');
                    RETURN NEW;
                END IF;

                IF (TG_OP = 'DELETE') THEN
                    RETURN OLD;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER weightloss_solutions_cache_trigger
            AFTER INSERT OR UPDATE OR DELETE ON solutions
            FOR EACH ROW
            EXECUTE FUNCTION weightloss_solutions_cache();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS weightloss_solutions_cache_trigger ON solutions');
        DB::unprepared('DROP FUNCTION IF EXISTS weightloss_solutions_cache');
    }
};