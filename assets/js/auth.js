document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.togglePassword);
        if (!input) {
            return;
        }

        const showing = input.type === 'password';
        input.type = showing ? 'text' : 'password';
        button.setAttribute('aria-label', showing ? 'Şifreyi gizle' : 'Şifreyi göster');
        button.innerHTML = `<i class="fa-regular ${showing ? 'fa-eye-slash' : 'fa-eye'}"></i>`;
    });
});

const registerForm = document.getElementById('registerForm');
if (registerForm) {
    const password = document.getElementById('password');
    const confirmation = document.getElementById('confirm_password');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    const matchText = document.getElementById('passwordMatch');

    const updatePasswordState = () => {
        const value = password.value;
        const score = [
            value.length >= 6,
            value.length >= 10,
            /[A-Z]/.test(value) && /[a-z]/.test(value),
            /\d/.test(value),
            /[^A-Za-z0-9]/.test(value)
        ].filter(Boolean).length;
        const levels = [
            { width: '0%', color: 'bg-slate-300', label: 'Şifreniz en az 6 karakter olmalıdır.' },
            { width: '25%', color: 'bg-rose-500', label: 'Zayıf şifre' },
            { width: '50%', color: 'bg-orange-500', label: 'Orta güçte şifre' },
            { width: '75%', color: 'bg-amber-500', label: 'İyi şifre' },
            { width: '100%', color: 'bg-emerald-500', label: 'Güçlü şifre' }
        ];
        const level = value.length === 0 ? levels[0] : levels[Math.min(score, 4)];
        strengthFill.className = `h-full rounded-full transition-all duration-300 ${level.color}`;
        strengthFill.style.width = level.width;
        strengthText.textContent = level.label;

        const mismatch = confirmation.value.length > 0 && password.value !== confirmation.value;
        confirmation.setCustomValidity(mismatch ? 'Şifreler eşleşmiyor.' : '');
        matchText.textContent = confirmation.value.length === 0
            ? 'Şifreleri aynı girdiğinizden emin olun.'
            : (mismatch ? 'Şifreler eşleşmiyor.' : 'Şifreler eşleşiyor.');
        matchText.className = `-mt-2 text-xs ${confirmation.value.length === 0 ? 'text-slate-400' : (mismatch ? 'text-rose-600' : 'text-emerald-600')}`;
    };

    password.addEventListener('input', updatePasswordState);
    confirmation.addEventListener('input', updatePasswordState);
    updatePasswordState();
}

document.querySelectorAll('#loginForm, #registerForm').forEach((form) => {
    form.addEventListener('submit', () => {
        if (!form.checkValidity()) {
            return;
        }

        const button = form.querySelector('button[type="submit"]');
        if (button) {
            button.disabled = true;
            button.querySelector('span').textContent = 'Gönderiliyor...';
            button.querySelector('i').className = 'fa-solid fa-spinner fa-spin';
        }
    });
});
