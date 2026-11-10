// assets/js/tracker.js
(function() {
    'use strict';
    
    // Get configuration from script attributes
    const script = document.currentScript || document.querySelector('script[data-api-key]');
    const apiKey = script.getAttribute('data-api-key');
    let apiUrl = script.getAttribute('data-api-url');
    
    // Eğer API URL belirtilmemişse, current domain kullan
    if (!apiUrl) {
        // Script src'den domain'i çıkar
        const scriptSrc = script.src;
        if (scriptSrc) {
            const url = new URL(scriptSrc);
            apiUrl = url.origin + '/api';
        } else {
            apiUrl = window.location.origin + '/api';
        }
    }
    
    if (!apiKey) {
        console.error('Network Analytics: API key not found');
        return;
    }
    
    // Generate visitor ID
    let visitorId = localStorage.getItem('na_visitor_id');
    if (!visitorId) {
        visitorId = 'visitor-' + Math.random().toString(36).substr(2, 9) + '-' + Date.now();
        localStorage.setItem('na_visitor_id', visitorId);
    }
    
    // Generate session ID
    let sessionId = sessionStorage.getItem('na_session_id');
    if (!sessionId) {
        sessionId = 'session-' + Math.random().toString(36).substr(2, 9) + '-' + Date.now();
        sessionStorage.setItem('na_session_id', sessionId);
    }
    
    // Utility functions
    function getDeviceType() {
        const width = window.innerWidth;
        if (width < 768) return 'mobile';
        if (width < 1024) return 'tablet';
        return 'desktop';
    }
    
    function getBrowserInfo() {
        const ua = navigator.userAgent;
        let browser = 'Unknown';
        let os = 'Unknown';
        
        // Browser detection
        if (ua.indexOf('Chrome') > -1) browser = 'Chrome';
        else if (ua.indexOf('Firefox') > -1) browser = 'Firefox';
        else if (ua.indexOf('Safari') > -1) browser = 'Safari';
        else if (ua.indexOf('Edge') > -1) browser = 'Edge';
        else if (ua.indexOf('Opera') > -1) browser = 'Opera';
        
        // OS detection
        if (ua.indexOf('Windows') > -1) os = 'Windows';
        else if (ua.indexOf('Mac') > -1) os = 'macOS';
        else if (ua.indexOf('Linux') > -1) os = 'Linux';
        else if (ua.indexOf('Android') > -1) os = 'Android';
        else if (ua.indexOf('iOS') > -1) os = 'iOS';
        
        return { browser, os };
    }
    
    // Send data to API - Geliştirilmiş versiyon
    function sendData(endpoint, data) {
        // API key'i data'ya ekle
        data.api_key = apiKey;
        
        const url = `${apiUrl}/track/${endpoint}`;
        
        // Fetch ile gönder
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'text/plain;charset=UTF-8',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data),
            mode: 'cors', // CORS modu açık
            credentials: 'omit' // Credentials gönderme
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }
            return response.json();
        })
        .then(function(result) {
            if (result.error) {
                console.error('API Error:', result.error);
            }
        })
        .catch(function(error) {
            console.error('Network Analytics Error:', error);
        });
    }
    
    let lastTrackedUrl = null;
    let pageReferrer = document.referrer || '';

    // Track page view
    function trackPageView() {
        const pageUrl = window.location.href;
        if (lastTrackedUrl !== null && lastTrackedUrl !== pageUrl) {
            pageReferrer = lastTrackedUrl;
            window.dispatchEvent(new CustomEvent('networkanalytics:navigation', {
                detail: { previousUrl: lastTrackedUrl }
            }));
        }
        lastTrackedUrl = pageUrl;
        const browserInfo = getBrowserInfo();

        const data = {
            visitor_id: visitorId,
            session_id: sessionId,
            page_url: pageUrl,
            page_title: document.title,
            referrer_url: pageReferrer,
            user_agent: navigator.userAgent,
            browser: browserInfo.browser,
            os: browserInfo.os,
            device_type: getDeviceType(),
            screen_resolution: `${screen.width}x${screen.height}`,
            viewport_size: `${window.innerWidth}x${window.innerHeight}`,
            language: navigator.language || 'en-US'
        };
        
        sendData('pageview', data);
    }

    function trackSpaNavigation() {
        let scheduled = false;
        function checkNavigation() {
            if (scheduled) return;
            scheduled = true;
            setTimeout(function() {
                scheduled = false;
                if (window.location.href !== lastTrackedUrl) {
                    trackPageView();
                }
            }, 0);
        }

        ['pushState', 'replaceState'].forEach(function(method) {
            const original = window.history[method];
            window.history[method] = function() {
                const result = original.apply(this, arguments);
                checkNavigation();
                return result;
            };
        });
        window.addEventListener('popstate', checkNavigation);
        window.addEventListener('hashchange', checkNavigation);
    }

    // Track custom event
    function trackEvent(eventType, category, action, label, value, pageUrl) {
        const data = {
            visitor_id: visitorId,
            session_id: sessionId,
            event_type: eventType || 'custom',
            event_category: category,
            event_action: action,
            event_label: label,
            event_value: value,
            page_url: pageUrl || window.location.href
        };
        
        sendData('event', data);
    }
    
    // Track performance metrics
    function trackPerformance() {
        if ('performance' in window) {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const perfData = performance.timing;
                    const loadTime = perfData.loadEventEnd - perfData.navigationStart;
                    const domReady = perfData.domContentLoadedEventEnd - perfData.navigationStart;
                    
                    if (loadTime > 0) {
                        trackEvent('performance', 'page-load', 'load-time', window.location.pathname, loadTime);
                    }
                    
                    if (domReady > 0) {
                        trackEvent('performance', 'dom-ready', 'time', window.location.pathname, domReady);
                    }
                    
                    // First paint
                    if ('getEntriesByType' in performance) {
                        const paintEntries = performance.getEntriesByType('paint');
                        paintEntries.forEach(function(entry) {
                            trackEvent('performance', 'paint', entry.name, window.location.pathname, Math.round(entry.startTime));
                        });
                    }
                }, 100);
            });
        }
    }
    
    // Track scroll depth
    function trackScrollDepth() {
        let maxScroll = 0;
        const milestones = [25, 50, 75, 100];
        const reached = [];

        window.addEventListener('networkanalytics:navigation', function() {
            maxScroll = 0;
            reached.length = 0;
        });

        function checkScroll() {
            const scrollTop = window.pageYOffset;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            
            if (docHeight <= 0) return; // Sayfa çok kısa
            
            const scrollPercent = Math.round((scrollTop / docHeight) * 100);
            
            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;
                
                milestones.forEach(function(milestone) {
                    if (scrollPercent >= milestone && reached.indexOf(milestone) === -1) {
                        reached.push(milestone);
                        trackEvent('scroll', 'scroll-depth', 'reached', milestone + '%', milestone);
                    }
                });
            }
        }
        
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    checkScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }
    
    // Track time on page
    function trackTimeOnPage() {
        let startTime = Date.now();
        let isActive = true;
        
        function sendTimeOnPage() {
            const timeSpent = Math.round((Date.now() - startTime) / 1000);
            if (timeSpent > 0 && isActive) {
                trackEvent('timing', 'time-on-page', 'duration', window.location.pathname, timeSpent);
            }

            window.addEventListener('networkanalytics:navigation', function(event) {
                const timeSpent = Math.round((Date.now() - startTime) / 1000);
                if (timeSpent > 0 && isActive) {
                    trackEvent('timing', 'time-on-page', 'duration', event.detail.previousUrl, timeSpent, event.detail.previousUrl);
                }
                startTime = Date.now();
            });
        }
        
        // Visibility API - sayfa görünür mü kontrol et
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                isActive = false;
                sendTimeOnPage();
            } else {
                isActive = true;
                startTime = Date.now(); // Reset timer
            }
        });
        
        // Send time on page before leaving
        window.addEventListener('beforeunload', sendTimeOnPage);
        
        // Send time periodically for long sessions
        setInterval(function() {
            if (isActive) {
                const timeSpent = Math.round((Date.now() - startTime) / 1000);
                if (timeSpent > 0 && timeSpent % 30 === 0) { // Every 30 seconds
                    trackEvent('timing', 'time-on-page', 'ping', window.location.pathname, timeSpent);
                }
            }
        }, 1000);
    }
    
    // Initialize tracking
    function init() {
        // Track page view immediately
        trackPageView();
        trackSpaNavigation();
        
        // Track performance metrics
        trackPerformance();
        
        // Track scroll depth
        trackScrollDepth();
        
        // Track time on page
        trackTimeOnPage();
        
        // Make trackEvent globally available
        window.NetworkAnalytics = {
            trackEvent: trackEvent,
            trackPageView: trackPageView,
            visitorId: visitorId,
            sessionId: sessionId
        };
        
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();