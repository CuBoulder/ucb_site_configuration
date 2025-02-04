(function (drupalSettings) {
  const settings = drupalSettings.service_goodkind[0];
  if (!settings) return;

  console.log(settings)
  // Set config BEFORE loading the script
  window.gkCBWConfig = {
    aiBotId: settings.ai_bot_id,
    apiKey: settings.ai_bot_api_key,
    domain: 'https://www.colorado.edu'
  };

  // Load the Goodkind widget
  const script = document.createElement('script');
  script.type = 'module';
  script.src = 'https://widget.goodkind.com/gk-chatbot-widget.js';
  script.defer = true;

  // Append script to body
  const body = document.body || document.getElementsByTagName('body')[0];
  body.appendChild(script);
})(window.drupalSettings);
