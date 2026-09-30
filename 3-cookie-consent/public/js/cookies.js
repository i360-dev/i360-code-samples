// Cookie Consent Management
(function() {
    'use strict';

    function init() {
        const cookieBanner = document.getElementById('cookieConsent');
        const cookieModal = document.getElementById('cookieModal');

        if (!cookieBanner || !cookieModal) {
            console.warn('Cookie consent elements not found on this page.');
            return;
        }

        const acceptBtn = document.getElementById('cookieAccept');
        const rejectBtn = document.getElementById('cookieReject');
        const settingsBtn = document.getElementById('cookieSettings');
        const closeBtn = document.getElementById('closeModal');
        const saveBtn = document.getElementById('savePreferences');
        const analyticsToggle = document.getElementById('analyticsCookies');
        const marketingToggle = document.getElementById('marketingCookies');
        const functionalToggle = document.getElementById('functionalCookies');

        const CONSENT_VERSION = 1; // bump if the cookie policy changes materially
        const stored = localStorage.getItem('cookieConsent');
        let cookieConsent = null;
        if (stored) {
            try {
                const parsed = JSON.parse(stored);
                if (parsed.version === CONSENT_VERSION) {
                    cookieConsent = parsed;
                }
            } catch (e) {
                cookieConsent = null;
            }
        }

        if (!cookieConsent) {
            setTimeout(() => { cookieBanner.style.display = 'block'; }, 1000);
        } else {
            applyPreferences(cookieConsent);
        }

        acceptBtn.addEventListener('click', function() {
            const preferences = { essential: true, analytics: true, marketing: true, functional: true, version: CONSENT_VERSION, timestamp: new Date().toISOString() };
            savePreferences(preferences);
            cookieBanner.style.display = 'none';
            applyPreferences(preferences);
        });

        rejectBtn.addEventListener('click', function() {
            const preferences = { essential: true, analytics: false, marketing: false, functional: false, version: CONSENT_VERSION, timestamp: new Date().toISOString() };
            savePreferences(preferences);
            cookieBanner.style.display = 'none';
        });

        settingsBtn.addEventListener('click', function() {
            cookieModal.style.display = 'flex';
            if (cookieConsent) {
                analyticsToggle.checked = cookieConsent.analytics || false;
                marketingToggle.checked = cookieConsent.marketing || false;
                functionalToggle.checked = cookieConsent.functional || false;
            }
        });

        closeBtn.addEventListener('click', function() { cookieModal.style.display = 'none'; });

        cookieModal.addEventListener('click', function(e) {
            if (e.target === cookieModal) cookieModal.style.display = 'none';
        });

        saveBtn.addEventListener('click', function() {
            const preferences = {
                essential: true,
                analytics: analyticsToggle.checked,
                marketing: marketingToggle.checked,
                functional: functionalToggle.checked,
                version: CONSENT_VERSION,
                timestamp: new Date().toISOString()
            };
            savePreferences(preferences);
            cookieBanner.style.display = 'none';
            cookieModal.style.display = 'none';
            applyPreferences(preferences);
            showToast('Your cookie preferences have been saved');
        });

        function savePreferences(preferences) {
            localStorage.setItem('cookieConsent', JSON.stringify(preferences));
            cookieConsent = preferences;
        }

        // Reads window.TrackingScripts (defined in tracking-scripts.js) and
        // fires only the categories the visitor consented to. Adding a new
        // script anywhere never requires touching this function again.
        function applyPreferences(prefs) {
            const registry = window.TrackingScripts || { analytics: [], marketing: [], functional: [] };
            ['analytics', 'marketing', 'functional'].forEach(function(category) {
                if (!prefs[category]) return;
                (registry[category] || []).forEach(function(script) {
                    try {
                        script.load();
                        console.log('Loaded ' + category + ' script: ' + script.name);
                    } catch (e) {
                        console.error('Failed to load ' + category + ' script: ' + script.name, e);
                    }
                });
            });
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 100px;
                right: 30px;
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                color: white;
                padding: 15px 25px;
                border-radius: 50px;
                box-shadow: 0 5px 20px rgba(0, 168, 232, 0.4);
                z-index: 10001;
                font-weight: 600;
                animation: slideInRight 0.5s ease;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.5s ease';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
