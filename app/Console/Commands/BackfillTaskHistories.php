<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\TaskHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillTaskHistories extends Command
{
    protected $signature = 'backfill:task-histories {--dry-run}';

    protected $description = 'Migrar el contenido de `detalle` de tasks a la tabla task_histories (comentarios)';

    public function handle(): int
    {
        $dry = $this->option('dry-run');

        $this->info('Buscando tasks para procesar...');

        $tasks = Task::all();
        $totalCreated = 0;

        foreach ($tasks as $task) {
            $detalle = $task->detalle;

            if (blank($detalle)) {
                continue;
            }

            $entries = preg_split('/\R{2,}/', trim($detalle));

            foreach ($entries as $entry) {
                $entry = trim($entry);
                if ($entry === '') {
                    continue;
                }

                // Intentar parsear formato: [dd/mm/YYYY HH:MM] Name: message
                if (preg_match('/^\[(\d{2}\/\d{2}\/\d{4} \d{2}:\d{2})\]\s+(.+?):\s+(.*)$/s', $entry, $m)) {
                    $date = Carbon::createFromFormat('d/m/Y H:i', $m[1])->toDateTimeString();
                    $author = trim($m[2]);
                    $message = trim($m[3]);

                    $comentario = "{$author}: {$message}";
                    $createdAt = $date;
                } else {
                    // Entrada sin timestamp/autor: usar todo como comentario y created_at = now
                    $comentario = $entry;
                    $createdAt = now();
                }

                // Evitar duplicados por task, comentario y created_at
                $exists = TaskHistory::where('task_id', $task->id)
                    ->where('comentario', $comentario)
                    ->where('created_at', $createdAt)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $this->line("Procesando task {$task->id}: creando historial: ".str($comentario)->limit(80));

                if (! $dry) {
                    TaskHistory::create([
                        'task_id' => $task->id,
                        'user_id' => null,
                        'tipo' => 'comentario',
                        'comentario' => $comentario,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    $totalCreated++;
                }
            }
        }

        $this->info("Completado. Entradas creadas: {$totalCreated}");

        return 0;
    }
}
