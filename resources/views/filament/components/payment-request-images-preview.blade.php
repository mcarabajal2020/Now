@php
    use Illuminate\Support\Facades\Storage;
    $items = is_array($images) ? $images : (is_null($images) ? [] : (array) $images);
@endphp

@if(! empty($items))
    <div class="grid grid-cols-3 gap-2 mb-4">
        @foreach($items as $img)
            @php
                $path = (string) $img;
                if (str_starts_with($path, 'http')) {
                    // intentar extraer ruta
                    $parsed = parse_url($path, PHP_URL_PATH) ?: $path;
                    if (str_contains($parsed, '/storage/')) {
                        $path = ltrim(substr($parsed, strpos($parsed, '/storage/') + strlen('/storage/')), '/');
                    }
                } elseif (str_contains($path, '/storage/')) {
                    $path = ltrim(substr($path, strpos($path, '/storage/') + strlen('/storage/')), '/');
                }

                $url = Storage::disk('public')->url($path);
                // Usar la parte de path para evitar host/puerto incorrecto (p. ej. http://localhost)
                $urlPath = parse_url($url, PHP_URL_PATH) ?: $url;
            @endphp

            <div class="rounded overflow-hidden border">
                <img src="{{ $urlPath }}" alt="imagen" class="w-full h-32 object-cover" />
            </div>
        @endforeach
    </div>
@endif
