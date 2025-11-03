(function (drupalSettings) {
  const settings = drupalSettings.service_salesforce?.[0];
  if (!settings) return;

  function initEmbeddedMessaging() {
    try {
      embeddedservice_bootstrap.settings.language = 'en_US';
      
      window.addEventListener("onEmbeddedMessagingReady", (event) => {
        embeddedservice_bootstrap.prechatAPI.setVisiblePrechatFields({
          // List the pre-chat field names with the value and whether
          // it's editable in the pre-chat form.
          "_firstName": {
            "value": "",
            "isEditableByEndUser": true
          }
        });
        embeddedservice_bootstrap.prechatAPI.setHiddenPrechatFields({"CurrentSite": window.location.href});
      });

      embeddedservice_bootstrap.init(
        settings.salesforce_id,
        settings.embedded_service_name,
        settings.endpoint_url,
        {
          scrt2URL: settings.scrt2_url
        }
      );
    } catch (err) {
      console.error('Error loading Embedded Messaging:', err);
    }
  }

  function loadSalesforceBootstrap() {
    const endpoint = settings.endpoint_url.replace(/\/$/, '');
    const script = document.createElement('script');
    script.src = `${endpoint}/assets/js/bootstrap.min.js`;
    script.type = 'text/javascript';
    script.onload = initEmbeddedMessaging;
    script.onerror = () => console.error('Failed to load Salesforce bootstrap.min.js');
    document.head.appendChild(script);
  }

  loadSalesforceBootstrap();
})(window.drupalSettings);
