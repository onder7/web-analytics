(function () {
    'use strict';

    function initializeIcons() {
        var icons = {
            'trending-up': 'fa-arrow-trend-up', 'layout-dashboard': 'fa-table-columns',
            'bar-chart-3': 'fa-chart-column', activity: 'fa-wave-square', users: 'fa-users',
            'file-text': 'fa-file-lines', 'globe-2': 'fa-earth-americas', settings: 'fa-gear',
            globe: 'fa-globe', 'log-out': 'fa-right-from-bracket', 'chevron-left': 'fa-chevron-left',
            x: 'fa-xmark', plus: 'fa-plus', eye: 'fa-eye', 'calendar-check': 'fa-calendar-check',
            radio: 'fa-tower-broadcast', 'user-plus': 'fa-user-plus', 'user-check': 'fa-user-check',
            'map-pin': 'fa-location-dot', monitor: 'fa-desktop', smartphone: 'fa-mobile-screen',
            tablet: 'fa-tablet-screen-button', search: 'fa-magnifying-glass', 'link-2': 'fa-link',
            'share-2': 'fa-share-nodes', compass: 'fa-compass', 'chevron-right': 'fa-chevron-right',
            'clock-3': 'fa-clock', 'code-2': 'fa-code', bell: 'fa-bell', copy: 'fa-copy',
            'shield-check': 'fa-shield-halved', shield: 'fa-shield-halved', menu: 'fa-bars',
            languages: 'fa-language',
            mail: 'fa-envelope', 'lock-keyhole': 'fa-key',
            download: 'fa-download', 'calendar-days': 'fa-calendar-days',
            'circle-alert': 'fa-circle-exclamation', 'circle-check': 'fa-circle-check',
            'triangle-alert': 'fa-triangle-exclamation', 'arrow-up-right': 'fa-arrow-up-right-from-square',
            'chart-no-axes-combined': 'fa-chart-line', pencil: 'fa-pen', 'trash-2': 'fa-trash-can',
            'arrow-left': 'fa-arrow-left', info: 'fa-circle-info', 'settings-2': 'fa-sliders',
            save: 'fa-floppy-disk'
        };
        document.querySelectorAll('[data-lucide]').forEach(function (icon) {
            var name = icon.getAttribute('data-lucide');
            icon.classList.add('fa-solid', icons[name] || 'fa-circle');
            icon.removeAttribute('data-lucide');
        });
    }

    function setupSidebar() {
        var sidebar = document.getElementById('analytics-sidebar');
        var collapseButton = document.getElementById('sidebar-collapse');
        var menuButton = document.getElementById('mobile-menu-button');
        var backdrop = document.getElementById('sidebar-backdrop');

        if (collapseButton && sidebar) {
            if (localStorage.getItem('analytics-sidebar-collapsed') === 'true') {
                sidebar.classList.add('is-collapsed');
            }
            collapseButton.addEventListener('click', function () {
                sidebar.classList.toggle('is-collapsed');
                var collapsed = sidebar.classList.contains('is-collapsed');
                localStorage.setItem('analytics-sidebar-collapsed', collapsed ? 'true' : 'false');
                document.querySelectorAll('.sidebar-chevron').forEach(function (icon) {
                    icon.classList.toggle('rotate-180', collapsed);
                });
            });
        }

        function closeMobileSidebar() {
            if (!sidebar || !backdrop) return;
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }

        if (menuButton && sidebar && backdrop) {
            menuButton.addEventListener('click', function () {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            });
            document.querySelectorAll('[data-sidebar-close]').forEach(function (element) {
                element.addEventListener('click', closeMobileSidebar);
            });
        }
    }

    function setupWebsiteSelectors() {
        document.querySelectorAll('[data-website-select]').forEach(function (select) {
            select.addEventListener('change', function () {
                var url = new URL(window.location.href);
                url.searchParams.set('website_id', select.value);
                window.location.assign(url.toString());
            });
        });
    }

    function setupDateRangeSelector() {
        var period = document.querySelector('[data-period-select]');
        var fields = document.querySelector('[data-custom-date-fields]');
        if (!period || !fields) return;

        var dates = fields.querySelectorAll('input[type="date"]');
        function updateCustomFields() {
            var isCustom = period.value === 'custom';
            fields.classList.toggle('hidden', !isCustom);
            fields.classList.toggle('flex', isCustom);
            dates.forEach(function (input) {
                input.required = isCustom;
            });
        }

        period.addEventListener('change', updateCustomFields);
        updateCustomFields();
    }

    function setupRealtimeActiveVisitors() {
        var counter = document.querySelector('[data-active-now-count]');
        var cardGroup = counter && counter.closest('[data-realtime-active-url]');
        if (!counter || !cardGroup) return;

        var endpoint = cardGroup.getAttribute('data-realtime-active-url');
        var inFlight = false;
        function refreshCount() {
            if (inFlight) return;
            inFlight = true;
            fetch(endpoint, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
                cache: 'no-store'
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('Canlı ziyaretçi sayısı alınamadı (HTTP ' + response.status + ').');
                }
                return response.json();
            }).then(function (data) {
                if (!Number.isInteger(data.active_now) || data.active_now < 0) {
                    throw new Error('Canlı ziyaretçi yanıtı geçersiz.');
                }
                counter.textContent = new Intl.NumberFormat('tr-TR').format(data.active_now);
            }).catch(function (error) {
                console.error('Canlı ziyaretçi sayısı güncellenemedi.', error);
            }).then(function () {
                inFlight = false;
            });
        }

        refreshCount();
        window.setInterval(refreshCount, 30000);
    }

    function setupSmtpTest() {
        var form = document.querySelector('[data-notification-form]');
        var button = form && form.querySelector('[data-smtp-test]');
        var result = form && form.querySelector('[data-smtp-test-result]');
        if (!form || !button || !result) return;

        var label = button.textContent.trim();
        var testingLabel = button.getAttribute('data-testing-label') || label;
        button.addEventListener('click', function () {
            if (!form.reportValidity() || button.disabled) return;

            var data = new FormData(form);
            button.disabled = true;
            button.textContent = testingLabel;
            result.textContent = '';
            result.className = 'hidden text-sm';

            fetch('/analytics/notifications/test-smtp', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
                body: data
            }).then(function (response) {
                return response.json().then(function (payload) {
                    if (!response.ok) {
                        throw new Error(payload.message || 'SMTP test failed.');
                    }
                    return payload;
                });
            }).then(function (payload) {
                result.textContent = payload.message;
                result.className = 'text-sm text-emerald-700';
                result.setAttribute('role', 'status');
            }).catch(function (error) {
                result.textContent = error.message || 'SMTP test failed.';
                result.className = 'text-sm text-rose-700';
                result.setAttribute('role', 'alert');
            }).then(function () {
                button.disabled = false;
                button.textContent = label;
            });
        });
    }

    function drawTrendChart() {
        var chart = document.getElementById('trend-chart');
        var dataElement = document.getElementById('trend-data');
        if (!chart || !dataElement) return;

        var data;
        try {
            data = JSON.parse(dataElement.textContent || '[]');
        } catch (error) {
            console.error('Grafik verisi okunamadı.', error);
            return;
        }
        if (!Array.isArray(data) || data.length === 0) return;

        var width = 760;
        var height = 280;
        var pad = { top: 20, right: 20, bottom: 36, left: 48 };
        var plotWidth = width - pad.left - pad.right;
        var plotHeight = height - pad.top - pad.bottom;
        var maxValue = Math.max.apply(null, data.map(function (item) {
            return Math.max(Number(item.views) || 0, Number(item.visitors) || 0);
        })) * 1.12 || 1;
        var namespace = 'http://www.w3.org/2000/svg';

        function element(name, attributes, text) {
            var node = document.createElementNS(namespace, name);
            Object.keys(attributes || {}).forEach(function (key) {
                node.setAttribute(key, attributes[key]);
            });
            if (text) node.textContent = text;
            return node;
        }

        function pointList(key) {
            return data.map(function (item, index) {
                var x = pad.left + (data.length > 1 ? index * plotWidth / (data.length - 1) : plotWidth / 2);
                var value = Number(item[key]) || 0;
                var y = pad.top + plotHeight - value / maxValue * plotHeight;
                return { x: x, y: y, value: value };
            });
        }

        function line(points) {
            return points.map(function (point, index) {
                return (index === 0 ? 'M ' : 'L ') + point.x.toFixed(1) + ' ' + point.y.toFixed(1);
            }).join(' ');
        }

        var views = pointList('views');
        var visitors = pointList('visitors');
        var gradient = element('defs');
        var blueGradient = element('linearGradient', { id: 'trend-blue-gradient', x1: '0', y1: '0', x2: '0', y2: '1' });
        blueGradient.appendChild(element('stop', { offset: '0%', 'stop-color': '#3b82f6', 'stop-opacity': '.2' }));
        blueGradient.appendChild(element('stop', { offset: '100%', 'stop-color': '#3b82f6', 'stop-opacity': '0' }));
        var greenGradient = element('linearGradient', { id: 'trend-green-gradient', x1: '0', y1: '0', x2: '0', y2: '1' });
        greenGradient.appendChild(element('stop', { offset: '0%', 'stop-color': '#10b981', 'stop-opacity': '.16' }));
        greenGradient.appendChild(element('stop', { offset: '100%', 'stop-color': '#10b981', 'stop-opacity': '0' }));
        gradient.appendChild(blueGradient);
        gradient.appendChild(greenGradient);
        chart.appendChild(gradient);

        for (var tick = 0; tick <= 5; tick++) {
            var y = pad.top + plotHeight * tick / 5;
            chart.appendChild(element('line', { x1: pad.left, y1: y, x2: width - pad.right, y2: y, stroke: '#f1f5f9' }));
            var labelValue = Math.round(maxValue * (5 - tick) / 5);
            chart.appendChild(element('text', { x: pad.left - 10, y: y + 4, 'text-anchor': 'end', fill: '#94a3b8', 'font-size': '10' }, labelValue >= 1000 ? Math.round(labelValue / 1000) + 'K' : String(labelValue)));
        }

        var labelEvery = Math.max(1, Math.ceil(data.length / 8));
        data.forEach(function (item, index) {
            if (index % labelEvery !== 0) return;
            var x = pad.left + (data.length > 1 ? index * plotWidth / (data.length - 1) : plotWidth / 2);
            chart.appendChild(element('text', { x: x, y: height - 12, 'text-anchor': 'middle', fill: '#94a3b8', 'font-size': '10' }, item.label || ''));
        });

        [
            { points: views, color: '#3b82f6', gradient: 'url(#trend-blue-gradient)' },
            { points: visitors, color: '#10b981', gradient: 'url(#trend-green-gradient)' }
        ].forEach(function (series) {
            var first = series.points[0];
            var last = series.points[series.points.length - 1];
            var areaPath = line(series.points) + ' L ' + last.x.toFixed(1) + ' ' + (pad.top + plotHeight) + ' L ' + first.x.toFixed(1) + ' ' + (pad.top + plotHeight) + ' Z';
            chart.appendChild(element('path', { d: areaPath, fill: series.gradient }));
            chart.appendChild(element('path', { d: line(series.points), fill: 'none', stroke: series.color, 'stroke-width': '2.5', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }));
        });
    }

    function setupTableControls() {
        var visitorFilter = 'all';
        var visitorSearch = document.querySelector('[data-filter-input="#visitors-table"]');
        var globalSearch = document.querySelector('[data-global-search]');

        function applyVisitorFilters() {
            var query = [visitorSearch ? visitorSearch.value : '', globalSearch ? globalSearch.value : '']
                .join(' ').toLocaleLowerCase('tr-TR');
            document.querySelectorAll('#visitors-table tbody tr[data-visitor-type]').forEach(function (row) {
                var matchesType = visitorFilter === 'all' || row.getAttribute('data-visitor-type') === visitorFilter;
                var matchesSearch = row.textContent.toLocaleLowerCase('tr-TR').indexOf(query) !== -1;
                row.hidden = !matchesType || !matchesSearch;
            });
        }

        document.querySelectorAll('[data-filter-input]').forEach(function (input) {
            input.addEventListener('input', function () {
                var table = document.querySelector(input.getAttribute('data-filter-input'));
                if (!table) return;
                if (table.id === 'visitors-table') {
                    applyVisitorFilters();
                    return;
                }
                var query = input.value.toLocaleLowerCase('tr-TR');
                table.querySelectorAll('tbody tr').forEach(function (row) {
                    row.hidden = row.textContent.toLocaleLowerCase('tr-TR').indexOf(query) === -1;
                });
            });
        });

        document.querySelectorAll('[data-visitor-filter]').forEach(function (button) {
            button.addEventListener('click', function () {
                visitorFilter = button.getAttribute('data-visitor-filter');
                document.querySelectorAll('[data-visitor-filter]').forEach(function (item) {
                    item.classList.toggle('bg-white', item === button);
                    item.classList.toggle('text-slate-800', item === button);
                    item.classList.toggle('shadow-sm', item === button);
                    item.classList.toggle('text-slate-500', item !== button);
                });
                applyVisitorFilters();
            });
        });

        document.querySelectorAll('[data-sort-table] th[data-sort]').forEach(function (heading) {
            heading.addEventListener('click', function () {
                var table = heading.closest('table');
                var body = table && table.querySelector('tbody');
                if (!body) return;
                var index = Array.prototype.indexOf.call(heading.parentElement.children, heading);
                var ascending = heading.getAttribute('aria-sort') !== 'ascending';
                table.querySelectorAll('th[data-sort]').forEach(function (cell) {
                    cell.removeAttribute('aria-sort');
                });
                heading.setAttribute('aria-sort', ascending ? 'ascending' : 'descending');
                Array.prototype.slice.call(body.rows).sort(function (a, b) {
                    var left = a.cells[index].getAttribute('data-value') || a.cells[index].textContent.trim();
                    var right = b.cells[index].getAttribute('data-value') || b.cells[index].textContent.trim();
                    var leftNumber = Number(left);
                    var rightNumber = Number(right);
                    var comparison = !Number.isNaN(leftNumber) && !Number.isNaN(rightNumber)
                        ? leftNumber - rightNumber
                        : left.localeCompare(right, 'tr');
                    return ascending ? comparison : -comparison;
                }).forEach(function (row) {
                    body.appendChild(row);
                });
            });
        });

        document.querySelectorAll('[data-tab-button]').forEach(function (button) {
            button.addEventListener('click', function () {
                var tab = button.getAttribute('data-tab-button');
                document.querySelectorAll('[data-tab-button]').forEach(function (item) {
                    item.classList.toggle('bg-blue-50', item === button);
                    item.classList.toggle('text-blue-600', item === button);
                    item.classList.toggle('text-slate-500', item !== button);
                });
                document.querySelectorAll('[data-tab-content]').forEach(function (content) {
                    content.hidden = content.getAttribute('data-tab-content') !== tab;
                });
            });
        });

        document.querySelectorAll('[data-copy-target]').forEach(function (button) {
            button.addEventListener('click', copyFromButton);
        });
        document.querySelectorAll('[data-copy-text]').forEach(function (button) {
            button.addEventListener('click', copyFromButton);
        });

        function copyFromButton() {
            var button = this;
            var text = button.getAttribute('data-copy-text');
            if (text === null) {
                var target = document.querySelector(button.getAttribute('data-copy-target'));
                text = target ? target.textContent : '';
            }
            if (!text) {
                console.error('Kopyalanacak içerik bulunamadı.');
                return;
            }

            function showCopyResult(success) {
                var label = button.querySelector('[data-copy-label]');
                if (label) {
                    label.textContent = success ? 'Kopyalandı' : 'Kopyalama başarısız';
                    window.setTimeout(function () {
                        label.textContent = button.getAttribute('data-copy-text') !== null
                            ? 'İzleme Kodunu Kopyala'
                            : 'Kopyala';
                    }, 1800);
                }
            }

            function fallbackCopy() {
                var field = document.createElement('textarea');
                field.value = text;
                field.setAttribute('readonly', '');
                field.style.position = 'fixed';
                field.style.opacity = '0';
                document.body.appendChild(field);
                field.select();
                var copied = false;
                try {
                    copied = document.execCommand('copy');
                } catch (error) {
                    console.error('Kopyalama başarısız oldu.', error);
                }
                document.body.removeChild(field);
                showCopyResult(copied);
                if (!copied) console.error('Tarayıcı panoya kopyalamayı desteklemiyor.');
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function () {
                    showCopyResult(true);
                }).catch(function (error) {
                    console.error('Panoya kopyalama başarısız oldu.', error);
                    fallbackCopy();
                });
            } else {
                fallbackCopy();
            }
        }

        document.querySelectorAll('[data-website-search]').forEach(function (input) {
            input.addEventListener('input', function () {
                var query = input.value.toLocaleLowerCase('tr-TR');
                var visibleCount = 0;
                document.querySelectorAll('.website-card').forEach(function (card) {
                    var visible = card.getAttribute('data-search').toLocaleLowerCase('tr-TR').indexOf(query) !== -1;
                    card.hidden = !visible;
                    if (visible) visibleCount++;
                });
                var emptyMessage = document.getElementById('website-empty-search');
                if (emptyMessage) emptyMessage.classList.toggle('hidden', visibleCount !== 0);
            });
        });

        if (globalSearch) {
            globalSearch.addEventListener('input', function () {
                var query = globalSearch.value.toLocaleLowerCase('tr-TR');
                var websiteCards = document.querySelectorAll('.website-card');
                if (websiteCards.length) {
                    var visibleCount = 0;
                    websiteCards.forEach(function (card) {
                        var visible = card.getAttribute('data-search').toLocaleLowerCase('tr-TR').indexOf(query) !== -1;
                        card.hidden = !visible;
                        if (visible) visibleCount++;
                    });
                    var emptyMessage = document.getElementById('website-empty-search');
                    if (emptyMessage) emptyMessage.classList.toggle('hidden', visibleCount !== 0);
                }
                document.querySelectorAll('table tbody tr:not([data-visitor-type])').forEach(function (row) {
                    row.hidden = row.textContent.toLocaleLowerCase('tr-TR').indexOf(query) === -1;
                });
                applyVisitorFilters();
            });
        }

        var exportButton = document.querySelector('[data-export-table]');
        var exportTable = document.querySelector('table');
        if (exportButton && !exportTable) {
            exportButton.classList.add('hidden');
        } else if (exportButton) {
            exportButton.addEventListener('click', function () {
                var table = document.querySelector('table');
                if (!table) return;
                var rows = Array.prototype.slice.call(table.querySelectorAll('tr')).filter(function (row) {
                    return !row.hidden;
                });
                var csv = rows.map(function (row) {
                    return Array.prototype.map.call(row.cells, function (cell) {
                        var value = cell.textContent.trim().replace(/\s+/g, ' ');
                        return '"' + value.replace(/"/g, '""') + '"';
                    }).join(',');
                }).join('\r\n');
                var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.href = url;
                link.download = 'webstats-' + document.title.toLocaleLowerCase('tr-TR').replace(/[^a-z0-9]+/g, '-') + '.csv';
                link.click();
                URL.revokeObjectURL(url);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initializeIcons();
        setupSidebar();
        setupWebsiteSelectors();
        setupDateRangeSelector();
        setupRealtimeActiveVisitors();
        setupSmtpTest();
        drawTrendChart();
        setupTableControls();
    });
})();
