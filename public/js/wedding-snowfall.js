/**
 * Ultra-Optimized Realistic Wedding Snowfall & Golden Shimmer Effect
 * High-performance, zero-dependency, mobile-friendly particle system
 * Designed for MyDream Wedding Organizer
 *
 * Optimizations for low-end devices (HP Kentang):
 * 1. Offscreen Sprite Pre-rendering (Snowflakes digambar 1x saja di memori, 60fps via hardware-accelerated drawImage)
 * 2. Zero per-frame shadowBlur (menghilangkan lag GPU/baterai panas)
 * 3. Dynamic Hardware & Mobile Detection (Otomatis kurangi partikel di HP kentang)
 * 4. Page Visibility API (Otomatis pause saat pindah tab / aplikasi di-minimize)
 */
(function(window) {
    'use strict';

    function initWeddingSnowfall(options) {
        // Cek prefers-reduced-motion
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        const isMobile = window.innerWidth < 768;
        // Deteksi HP kentang: cores <= 4 atau deviceMemory <= 2GB
        const isLowEnd = (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 4) ||
                         (navigator.deviceMemory && navigator.deviceMemory <= 2);

        // Alokasi jumlah partikel yang aman dan enteng:
        // HP kentang: 20-24 flakes
        // Mobile standar: 28 flakes
        // Desktop: 50-60 flakes
        const defaultCount = isLowEnd ? (isMobile ? 20 : 35) : (isMobile ? 28 : 55);

        options = Object.assign({
            canvasId: 'wedding-snowfall-canvas',
            particleCount: defaultCount,
            goldRatio: 0.28,
            speed: 0.60,
            wind: 0.18,
            zIndex: 99
        }, options || {});

        let canvas = document.getElementById(options.canvasId);
        if (!canvas) {
            canvas = document.createElement('canvas');
            canvas.id = options.canvasId;
            document.body.appendChild(canvas);
        }

        canvas.style.position = 'fixed';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.width = '100vw';
        canvas.style.height = '100vh';
        canvas.style.pointerEvents = 'none';
        canvas.style.zIndex = String(options.zIndex);

        const ctx = canvas.getContext('2d', { alpha: true });
        if (!ctx) return;

        let width = 0;
        let height = 0;
        let animationFrameId = null;
        let isPaused = false;

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }

        window.addEventListener('resize', resize, { passive: true });
        resize();

        // =========================================================================
        // OFFSCREEN SPRITE CACHING (PRE-RENDERING)
        // Gambar kristal salju 1x saja di memori, bukan hitung path setiap frame!
        // =========================================================================
        function createSnowflakeSprite(size, branches, color, glowColor) {
            const offCanvas = document.createElement('canvas');
            const pad = 8;
            const fullSize = size * 2 + pad * 2;
            offCanvas.width = fullSize;
            offCanvas.height = fullSize;
            const oCtx = offCanvas.getContext('2d');
            if (!oCtx) return offCanvas;

            const cx = fullSize / 2;
            const cy = fullSize / 2;

            oCtx.translate(cx, cy);
            oCtx.strokeStyle = color;
            oCtx.fillStyle = color;
            oCtx.lineCap = 'round';
            oCtx.lineWidth = size > 11 ? 1.6 : 1.2;

            if (glowColor) {
                oCtx.shadowBlur = 4;
                oCtx.shadowColor = glowColor;
            }

            // Titik tengah
            oCtx.beginPath();
            oCtx.arc(0, 0, Math.max(1, size * 0.14), 0, Math.PI * 2);
            oCtx.fill();

            // 6 Lengan kristal
            const arms = 6;
            const angleStep = (Math.PI * 2) / arms;

            for (let i = 0; i < arms; i++) {
                const armAngle = i * angleStep;
                const cos = Math.cos(armAngle);
                const sin = Math.sin(armAngle);

                oCtx.beginPath();
                oCtx.moveTo(0, 0);
                oCtx.lineTo(cos * size, sin * size);
                oCtx.stroke();

                if (branches >= 1) {
                    const d1 = size * 0.52;
                    const bLen1 = size * 0.32;
                    drawBranchV(oCtx, cos * d1, sin * d1, armAngle, bLen1);
                }

                if (branches >= 2) {
                    const d2 = size * 0.78;
                    const bLen2 = size * 0.22;
                    drawBranchV(oCtx, cos * d2, sin * d2, armAngle, bLen2);
                }
            }

            return {
                canvas: offCanvas,
                halfSize: fullSize / 2
            };
        }

        function drawBranchV(targetCtx, bx, by, armAngle, branchLen) {
            const bAngle1 = armAngle + Math.PI / 3.2;
            const bAngle2 = armAngle - Math.PI / 3.2;

            targetCtx.beginPath();
            targetCtx.moveTo(bx, by);
            targetCtx.lineTo(bx + Math.cos(bAngle1) * branchLen, by + Math.sin(bAngle1) * branchLen);
            targetCtx.moveTo(bx, by);
            targetCtx.lineTo(bx + Math.cos(bAngle2) * branchLen, by + Math.sin(bAngle2) * branchLen);
            targetCtx.stroke();
        }

        function createPuffSprite(radius, color, glowColor) {
            const offCanvas = document.createElement('canvas');
            const fullSize = radius * 2 + 8;
            offCanvas.width = fullSize;
            offCanvas.height = fullSize;
            const oCtx = offCanvas.getContext('2d');
            if (!oCtx) return { canvas: offCanvas, halfSize: fullSize / 2 };

            oCtx.beginPath();
            oCtx.arc(fullSize / 2, fullSize / 2, radius, 0, Math.PI * 2);
            oCtx.fillStyle = color;
            if (glowColor) {
                oCtx.shadowBlur = 3;
                oCtx.shadowColor = glowColor;
            }
            oCtx.fill();

            return {
                canvas: offCanvas,
                halfSize: fullSize / 2
            };
        }

        // Cache 6 macam sprite kristal (White & Gold)
        const sprites = {
            heroWhite: createSnowflakeSprite(14, 2, 'rgba(255, 255, 255, 0.95)', 'rgba(255, 255, 255, 0.7)'),
            heroGold: createSnowflakeSprite(14, 2, 'rgba(235, 205, 148, 0.95)', 'rgba(176, 141, 87, 0.8)'),
            medWhite: createSnowflakeSprite(9, 1, 'rgba(240, 248, 255, 0.90)', 'rgba(255, 255, 255, 0.5)'),
            medGold: createSnowflakeSprite(9, 1, 'rgba(218, 185, 136, 0.90)', 'rgba(176, 141, 87, 0.6)'),
            starWhite: createSnowflakeSprite(5, 0, 'rgba(255, 255, 255, 0.85)', 'rgba(255, 255, 255, 0.4)'),
            puffWhite: createPuffSprite(2.2, 'rgba(255, 255, 255, 0.80)', 'rgba(255, 255, 255, 0.4)')
        };

        // =========================================================================
        // PARTICLE LOGIC
        // =========================================================================
        class Snowflake {
            constructor() {
                this.reset(true);
            }

            reset(initial = false) {
                this.x = Math.random() * width;
                this.y = initial ? Math.random() * height : -35;

                const isGold = Math.random() < options.goldRatio;
                const randType = Math.random();

                if (randType > 0.70) {
                    this.sprite = isGold ? sprites.heroGold : sprites.heroWhite;
                    this.scale = Math.random() * 0.2 + 0.9;
                } else if (randType > 0.35) {
                    this.sprite = isGold ? sprites.medGold : sprites.medWhite;
                    this.scale = Math.random() * 0.25 + 0.85;
                } else if (randType > 0.15) {
                    this.sprite = sprites.starWhite;
                    this.scale = Math.random() * 0.3 + 0.8;
                } else {
                    this.sprite = sprites.puffWhite;
                    this.scale = 1;
                }

                this.baseAlpha = Math.random() * 0.35 + 0.60; // 0.60 - 0.95
                this.alpha = this.baseAlpha;
                this.vy = (Math.random() * 0.7 + 0.45) * options.speed;
                this.vx = (Math.random() * 0.3 - 0.15) + options.wind;

                this.angle = Math.random() * Math.PI * 2;
                this.spinSpeed = (Math.random() * 0.016 - 0.008);

                this.sway = Math.random() * Math.PI * 2;
                this.swaySpeed = Math.random() * 0.02 + 0.008;
                this.swayAmp = Math.random() * 1.2 + 0.4;
            }

            update() {
                this.sway += this.swaySpeed;
                this.x += this.vx + Math.sin(this.sway) * this.swayAmp;
                this.y += this.vy;
                this.angle += this.spinSpeed;

                if (this.y > height + 35 || this.x < -40 || this.x > width + 40) {
                    this.reset(false);
                }
            }

            draw() {
                // Sangat cepat di HP kentang: drawImage murni dengan GPU hardware acceleration
                ctx.save();
                ctx.translate(this.x, this.y);
                ctx.rotate(this.angle);
                if (this.scale !== 1) {
                    ctx.scale(this.scale, this.scale);
                }
                ctx.globalAlpha = this.alpha;
                ctx.drawImage(this.sprite.canvas, -this.sprite.halfSize, -this.sprite.halfSize);
                ctx.restore();
            }
        }

        const flakes = [];
        for (let i = 0; i < options.particleCount; i++) {
            flakes.push(new Snowflake());
        }

        // =========================================================================
        // ANIMATION LOOP WITH AUTO-PAUSE ON HIDDEN TAB
        // =========================================================================
        function animate() {
            if (!isPaused) {
                ctx.clearRect(0, 0, width, height);
                for (let i = 0; i < flakes.length; i++) {
                    flakes[i].update();
                    flakes[i].draw();
                }
            }
            animationFrameId = requestAnimationFrame(animate);
        }

        animate();

        // Pause animasi saat tab tidak aktif (hemat baterai 100% di HP)
        function handleVisibilityChange() {
            isPaused = document.hidden;
        }
        document.addEventListener('visibilitychange', handleVisibilityChange);

        return {
            destroy: function() {
                if (animationFrameId) cancelAnimationFrame(animationFrameId);
                window.removeEventListener('resize', resize);
                document.removeEventListener('visibilitychange', handleVisibilityChange);
                if (canvas && canvas.parentNode) canvas.parentNode.removeChild(canvas);
            }
        };
    }

    window.initWeddingSnowfall = initWeddingSnowfall;
})(window);
