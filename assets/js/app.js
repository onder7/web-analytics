
// assets/js/app.js
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert.alert-dismissible');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Confirm delete actions
    const deleteLinks = document.querySelectorAll('a[href*="/delete"]');
    deleteLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Bu işlemi gerçekleştirmek istediğinizden emin misiniz?')) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Real-time clock
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('tr-TR');
        const dateString = now.toLocaleDateString('tr-TR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const clockElement = document.getElementById('live-clock');
        if (clockElement) {
            clockElement.innerHTML = `
                <div class="text-end">
                    <small class="text-muted">${dateString}</small><br>
                    <strong>${timeString}</strong>
                </div>
            `;
        }
    }

    // Update clock every second
    setInterval(updateClock, 1000);
    updateClock();
});

// Utility functions
function formatNumber(num) {
    return new Intl.NumberFormat('tr-TR').format(num);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('tr-TR');
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('tr-TR');
}

function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Başarıyla kopyalandı!', 'success');
        }).catch(function(err) {
            console.error('Kopyalama hatası:', err);
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        document.execCommand('copy');
        showToast('Başarıyla kopyalandı!', 'success');
    } catch (err) {
        console.error('Kopyalama hatası:', err);
        showToast('Kopyalama başarısız!', 'danger');
    }

    document.body.removeChild(textArea);
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '1055';
    
    const bgClass = type === 'success' ? 'bg-success' : 
                   type === 'danger' ? 'bg-danger' : 
                   type === 'warning' ? 'bg-warning' : 'bg-info';
    
    toast.innerHTML = `
        <div class="toast-body ${bgClass} text-white rounded">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'danger' ? 'exclamation-triangle' : 'info'}"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        document.body.removeChild(toast);
    });
}

// Chart color themes
const chartColors = {
    primary: '#0d6efd',
    secondary: '#6c757d',
    success: '#198754',
    info: '#0dcaf0',
    warning: '#ffc107',
    danger: '#dc3545',
    light: '#f8f9fa',
    dark: '#212529'
};

const chartColorPalette = [
    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
    '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF',
    '#4BC0C0', '#36A2EB', '#FFCE56', '#FF6384'
];
