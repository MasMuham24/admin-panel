document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('formLogin');
    const btnSubmit = document.getElementById('btnSubmit');
    const inputs = document.querySelectorAll('.form-control-custom');

    // Submit handler
    form.addEventListener('submit', function () {
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menghubungkan...';
        btnSubmit.disabled = true;
    });

    // Shake card jika ada error
    const alertEl = document.querySelector('.alert-custom');
    if (alertEl) {
        const card = document.querySelector('.login-card');
        card.classList.add('shake');
        card.addEventListener('animationend', () => card.classList.remove('shake'));
    }

    // Ripple effect pada button
    btnSubmit.addEventListener('click', function (e) {
        const ripple = document.createElement('span');
        const rect = btnSubmit.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${e.clientX - rect.left - size / 2}px;
            top: ${e.clientY - rect.top - size / 2}px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.5s linear;
            pointer-events: none;
        `;
        btnSubmit.appendChild(ripple);
        ripple.addEventListener('animationend', () => ripple.remove());
    });

    // Tambah style ripple ke head
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to { transform: scale(2.5); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Focus efek pada icon
    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.closest('.input-icon-wrap')?.querySelector('.icon')?.style
                && (this.closest('.input-icon-wrap').querySelector('.icon').style.color = '#534AB7');
        });

        input.addEventListener('blur', function () {
            this.closest('.input-icon-wrap')?.querySelector('.icon')?.style
                && (this.closest('.input-icon-wrap').querySelector('.icon').style.color = '#9ca3af');
        });
    });

});
