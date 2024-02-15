(function (drupalSettings) {
  const items = drupalSettings['service_statuspage'];
  if (!items) return;
  const body = document.body || document.getElementsByTagName('body')[0];
  items.forEach(function (settings) {
    const script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = `https://${settings['page_id']}.statuspage.io/embed/script.js`;
    script.async = script.defer = true;
    body.appendChild(script);
  });
})(window.drupalSettings);
