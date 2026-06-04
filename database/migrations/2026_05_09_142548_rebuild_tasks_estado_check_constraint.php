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
        // Compatibilidad MySQL/SQLite
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        DB::table('tasks')
            ->where('estado', $previousFinalStatus)
            ->update(['estado' => $finalStatus]);

        DB::statement('DROP TABLE IF EXISTS tasks_new');

        DB::statement(<<<SQL
            CREATE TABLE tasks_new (

                id INT AUTO_INCREMENT PRIMARY KEY,
                titulo VARCHAR(255) NOT NULL,
                descripcion TEXT NOT NULL,
                detalle TEXT NULL,
                estado VARCHAR(255) NOT NULL,
                fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ultima_modificacion DATETIME NULL,
                usuario_solicita_id INT NOT NULL,
                asignado_a_id INT NULL,
                fecha_finalizacion DATETIME NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                CONSTRAINT tasks_estado_check CHECK (estado in ('Nuevo', 'En Proceso', '{$finalStatus}'))
            ) ENGINE=InnoDB;
            SQL);

        DB::statement(<<<'SQL'
            INSERT INTO tasks_new (
                id,

                titulo,
                descripcion,
                detalle,
                estado,
                fecha_creacion,
                ultima_modificacion,
                usuario_solicita_id,
                asignado_a_id,
                fecha_finalizacion,
                created_at,
                updated_at
            )
            SELECT
                id,
                titulo,
                descripcion,
                detalle,
                estado,
                fecha_creacion,
                ultima_modificacion,
                usuario_solicita_id,
                asignado_a_id,
                fecha_finalizacion,
                created_at,
                updated_at
            FROM tasks;
            SQL);

        DB::statement('DROP TABLE tasks');
        DB::statement('ALTER TABLE tasks_new RENAME TO tasks');
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }
};
