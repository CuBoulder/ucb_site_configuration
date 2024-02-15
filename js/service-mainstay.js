(function (drupalSettings) {
  // Only one Mainstay service is allowed per page.
  const settings = drupalSettings.service_mainstay[0];
  if (!settings) return;
  const body = document.body || document.getElementsByTagName('body')[0];
  window.admitHubBot = { botToken: settings.bot_token, collegeId: settings.college_id };
  const script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = 'https://webbot.mainstay.com/static/js/webchat.js';
  script.async = script.defer = true;
  body.appendChild(script);
})(window.drupalSettings);
