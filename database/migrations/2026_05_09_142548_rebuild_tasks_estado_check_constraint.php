<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->rebuildTasksTable(finalStatus: 'Finalizado', previousFinalStatus: 'Terminado');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->rebuildTasksTable(finalStatus: 'Terminado', previousFinalStatus: 'Finalizado');
    }

    private function rebuildTasksTable(string $finalStatus, string $previousFinalStatus): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::table('tasks')
            ->where('estado', $previousFinalStatus)
            ->update(['estado' => $finalStatus]);

        DB::statement(<<<SQL
            CREATE TABLE "tasks_new" (
                "id" integer primary key autoincrement not null,
                "titulo" varchar not null,
                "descripcion" text not null,
                "detalle" text,
                "estado" varchar check ("estado" in ('Nuevo', 'En Proceso', '{$finalStatus}')) not null,
                "fecha_creacion" datetime not null default CURRENT_TIMESTAMP,
                "ultima_modificacion" datetime,
                "usuario_solicita_id" integer not null,
                "asignado_a_id" integer,
                "fecha_finalizacion" datetime,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("usuario_solicita_id") references "users"("id"),
                foreign key("asignado_a_id") references "users"("id")
            )
            SQL);

        DB::statement(<<<'SQL'
            INSERT INTO "tasks_new" (
                "id",
                "titulo",
                "descripcion",
                "detalle",
                "estado",
                "fecha_creacion",
                "ultima_modificacion",
                "usuario_solicita_id",
                "asignado_a_id",
                "fecha_finalizacion",
                "created_at",
                "updated_at"
            )
            SELECT
                "id",
                "titulo",
                "descripcion",
                "detalle",
                "estado",
                "fecha_creacion",
                "ultima_modificacion",
                "usuario_solicita_id",
                "asignado_a_id",
                "fecha_finalizacion",
                "created_at",
                "updated_at"
            FROM "tasks"
            SQL);

        DB::statement('DROP TABLE "tasks"');
        DB::statement('ALTER TABLE "tasks_new" RENAME TO "tasks"');
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
