// =====================================================================
// Tracking Script Registry
// -----------------------------------------------------------------------
// Single place to add/remove third-party scripts. cookies.js reads this
// and only fires the scripts whose category the visitor has consented to.
//
// TO ADD GOOGLE ANALYTICS LATER: paste its snippet into a new entry in
// the `analytics` array below, following the ZoomInfo pattern. Nothing
// else in the codebase needs to change.
// =====================================================================

window.TrackingScripts = {
    analytics: [
        // {
        //     name: 'google-analytics',
        //     load: function () {
        //         var s = document.createElement('script');
        //         s.async = true;
        //         s.src = 'https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID';
        //         document.head.appendChild(s);
        //         window.dataLayer = window.dataLayer || [];
        //         function gtag(){ dataLayer.push(arguments); }
        //         gtag('js', new Date());
        //         gtag('config', 'GA_MEASUREMENT_ID');
        //     }
        // },
    ],

    marketing: [
        {
            name: 'ZoomInfo',
            load: function() {
                window["ZIProjectKey"] = "20687bf8b31786032765";
                var zi = document.createElement('script');
                zi.type = 'text/javascript';
                zi.async = true;
                zi.src = "https://js.zi-scripts.com/zi-tag.js";
                if (document.readyState === 'complete') {
                    document.body.appendChild(zi);
                } else {
                    window.addEventListener('load', function() {
                        document.body.appendChild(zi);
                    });
                }
            }
        }
        // Add GA entry here later, following this same pattern
    ],
};
