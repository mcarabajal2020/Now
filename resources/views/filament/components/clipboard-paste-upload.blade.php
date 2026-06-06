<script>
(function () {
    try {
        document.addEventListener('paste', async function (event) {
            if (! event.clipboardData) return;

            const items = Array.from(event.clipboardData.items || []);
            for (const item of items) {
                if (! item.type.startsWith('image/')) continue;

                const file = item.getAsFile ? item.getAsFile() : null;
                if (! file) continue;

                // Buscar input file cuyo name contenga 'imagenes'
                const input = document.querySelector('input[type="file"][name*="imagenes"]');
                if (! input) continue;

                // Construir DataTransfer con los archivos existentes + el pegado
                const dataTransfer = new DataTransfer();
                try {
                    for (let i = 0; i < input.files.length; i++) {
                        dataTransfer.items.add(input.files[i]);
                    }
                } catch (e) {
                    // ignore
                }
                dataTransfer.items.add(file);

                // Asignar y disparar change
                Object.defineProperty(input, 'files', {
                    value: dataTransfer.files,
                    writable: false,
                });

                const evt = new Event('change', { bubbles: true });
                input.dispatchEvent(evt);

                // Opcional: breve visual feedback
                try {
                    if (window.Filament && typeof window.Filament.notify === 'function') {
                        window.Filament.notify('Imagen pegada y agregada.');
                    }
                } catch (e) {
                    // noop
                }

                // Hemos procesado la primera imagen; ignorar el resto
                break;
            }
        });
    } catch (e) {
        console.error('clipboard paste upload error', e);
    }
})();
</script>
