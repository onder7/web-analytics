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
    
    console.log('Network Analytics: Using API URL:', apiUrl);
    
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
        
        // Debug log - API key kontrolü
        console.log('API Key:', apiKey);
        console.log('Sending to:', url, data);
        
        // Fetch ile gönder
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
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
                // Fallback tracking dene
                fallbackTracking(endpoint, data);
            } else {
                console.log('Analytics success:', result);
            }
        })
        .catch(function(error) {
            console.error('Network Analytics Error:', error);
            
            // Fallback: Image pixel tracking
            fallbackTracking(endpoint, data);
        });
    }
    
    // Fallback tracking method - Image pixel
    function fallbackTracking(endpoint, data) {
        try {
            const img = new Image();
            const params = new URLSearchParams();
            
            // Data'yı URL params'a çevir
            Object.keys(data).forEach(key => {
                if (data[key] !== null && data[key] !== undefined) {
                    params.append(key, data[key]);
                }
            });
            
            img.src = `${apiUrl}/track/${endpoint}?${params.toString()}`;
            img.style.display = 'none';
            document.body.appendChild(img);
            
            // Image'ı temizle
            setTimeout(() => {
                if (img.parentNode) {
                    img.parentNode.removeChild(img);
                }
            }, 1000);
            
            console.log('Fallback tracking used');
        } catch (e) {
            console.error('Fallback tracking failed:', e);
        }
    }
    
    // Track page view
    function trackPageView() {
        const browserInfo = getBrowserInfo();
        
        const data = {
            visitor_id: visitorId,
            session_id: sessionId,
            page_url: window.location.href,
            page_title: document.title,
            referrer_url: document.referrer,
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
    
    // Track custom event
    function trackEvent(eventType, category, action, label, value) {
        const data = {
            visitor_id: visitorId,
            session_id: sessionId,
            event_type: eventType || 'custom',
            event_category: category,
            event_action: action,
            event_label: label,
            event_value: value,
            page_url: window.location.href
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
        
        console.log('Network Analytics initialized with visitor:', visitorId);
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();