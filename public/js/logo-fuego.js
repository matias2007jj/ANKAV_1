// Llamas animadas del logo de Anka Fire (panel derecho del login).
// Dibuja el logo por franjas horizontales y las desplaza de lado a lado:
// la punta de las llamas se mueve más y la base de la "A" y el texto quedan quietos.
(function () {
    var wrap = document.getElementById('logoAnim');
    var img = document.getElementById('logoImg');
    var canvas = document.getElementById('logoCanvas');

    if (!wrap || !img || !canvas || !canvas.getContext) return;

    // Si la persona pidió menos movimiento en su sistema, se deja la imagen quieta.
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    function init() {
        var W = img.naturalWidth;
        var H = img.naturalHeight;
        if (!W) return;

        // Ajustes pensados para una imagen de 1100 px de ancho (se escalan solos).
        var k = W / 1100;
        var baseY = Math.round(560 * k);     // desde esta altura hacia abajo no se mueve nada
        var maxShift = 22 * k;               // cuánto se mueve la punta de las llamas (px)
        var padX = Math.round(24 * k);       // margen lateral del canvas
        var padTop = Math.round(40 * k);     // margen superior para que las llamas "crezcan"
        var step = Math.max(1, Math.round(2 * k)); // alto de cada franja

        canvas.width = W + padX * 2;
        canvas.height = H + padTop;
        canvas.style.left = (-padX / W * 100) + '%';
        canvas.style.width = ((W + padX * 2) / W * 100) + '%';

        var ctx = canvas.getContext('2d');

        function draw(t) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Las llamas se estiran y se encogen un poco, como si respiraran
            var s = 1 + 0.03 * Math.sin(t * 0.0021) + 0.015 * Math.sin(t * 0.0057 + 1);

            for (var y = 0; y < baseY; y += step) {
                var h = Math.min(step, baseY - y);
                var p = 1 - y / baseY;                         // 1 arriba, 0 en la base
                var amp = maxShift * Math.pow(p, 1.5);

                // Tres ondas mezcladas para que no se vea un vaivén regular
                var wave =
                    0.55 * Math.sin((y / k) * 0.020 + t * 0.0032) +
                    0.30 * Math.sin((y / k) * 0.047 + t * 0.0051 + 1.7) +
                    0.15 * Math.sin((y / k) * 0.090 + t * 0.0080 + 0.6);

                var dy = padTop + baseY - (baseY - y) * s;
                ctx.drawImage(img, 0, y, W, h, padX + amp * wave, dy, W, h * s + 1);
            }

            // Parte de abajo (patas de la A, nombre y lema): fija
            ctx.drawImage(img, 0, baseY, W, H - baseY, padX, padTop + baseY, W, H - baseY);
        }

        var running = false;
        var raf = 0;

        function loop(now) {
            if (!running) return;
            draw(now);
            raf = requestAnimationFrame(loop);
        }

        function start() {
            if (running) return;
            running = true;
            raf = requestAnimationFrame(loop);
        }

        function stop() {
            running = false;
            cancelAnimationFrame(raf);
        }

        draw(0);
        wrap.classList.add('is-live');

        // Solo anima cuando el panel se ve (en móvil está oculto)
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) start(); else stop();
            }).observe(wrap);
        } else {
            start();
        }
    }

    if (img.complete && img.naturalWidth) {
        init();
    } else {
        img.addEventListener('load', init);
    }
})();
