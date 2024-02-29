(function (drupalSettings) {
  // Only one LiveChat service is allowed per page.
  const settings = drupalSettings.service_livechat[0];
  if (!settings) return;
  const body = document.body || document.getElementsByTagName('body')[0];
  window.__lc = { license: settings.license_id };
  const script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = 'https://cdn.livechatinc.com/tracking.js';
  script.async = script.defer = true;
  body.appendChild(script);
})(window.drupalSettings);
