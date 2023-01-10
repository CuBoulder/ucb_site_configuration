(function(Drupal) {
	Drupal.behaviors.loadAdminHelpscountBeacon = {
		attach: function (context, drupalSettings) {
			const helpElement = document.querySelector('.menu-item__help-main a');
			if(!helpElement) return;
			const settings = drupalSettings['ucb_site_configuration']['admin_helpscout_beacon'];
			!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});
			window.Beacon('init', '1bb01225-0287-4443-a7e5-be8f375a7766');
			window.Beacon('prefill', settings['prefill']);
			window.Beacon('config', {
				'display': {
					'style': 'manual',
					'iconImage': 'message'
				}
			});
			helpElement.onclick = function(event) {
				event.preventDefault();
				window.Beacon('open');
			};
		}
	};
})(Drupal);
