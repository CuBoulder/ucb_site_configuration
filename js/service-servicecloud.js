(function (drupalSettings) {
  // Only one Service Cloud service is allowed per page.
  const settings = drupalSettings.service_servicecloud[0];
  if (!settings) return;
  const body = document.body || document.getElementsByTagName("body")[0];
  const enableAutoOpen = settings.auto_open;
  const autoOpenTime = settings.auto_open_delay;
  const enableEyeCatcher = settings.eyecatcher;

  // Initialize embedded_svc.
  const script = document.createElement("script");
  script.src = "https://cu.my.salesforce.com/embeddedservice/5.0/esw.min.js";
  script.async = script.defer = true;
  script.onload = function () {
    initESW("https://service.force.com");
    // Auto Open and Eye Catcher should be enabled only on desktop browsers.
    if (
      !/Android|webOS|iPhone|iPad|iPod|mobile.+firefox|BlackBerry|IEMobile|Opera Mini/i.test(
        navigator.userAgent
      )
    ) {
      if (enableAutoOpen) {
        setTimeout(function () {
          closeEyeCatcherAndBootstrap(
            document.querySelector(".service-servicecloud-eyecatcher")
          );
        }, autoOpenTime * 1000);
      }

      if (enableEyeCatcher) {
        addEyeCatcherHtml();
      }
    }
  };
  body.appendChild(script);

  function initESW(gslbBaseURL) {
    embedded_svc.settings.displayHelpButton = true; //Or false
    embedded_svc.settings.language = ""; //For example, enter 'en' or 'en-US'
    embedded_svc.settings.defaultMinimizedText = "Questions? Let's Chat"; //(Defaults to Chat with an Expert)
    embedded_svc.settings.disabledMinimizedText = "Liaison Unavailable"; //(Defaults to Agent Offline)
    embedded_svc.settings.offlineSupportMinimizedText = "Questions? Let's Chat"; //(Defaults to Contact Us)
    embedded_svc.settings.enabledFeatures = ["LiveAgent"];
    embedded_svc.settings.entryFeature = "LiveAgent";
    embedded_svc.settings.extraPrechatFormDetails = [
      {
        label: "Current Site",
        value: location.href,
        transcriptFields: ["Current_Site__c"],
        displayToAgent: true,
      },
    ];
    embedded_svc.settings.extraPrechatInfo = [
      {
        entityFieldMaps: [
          {
            doCreate: false,
            doFind: true,
            fieldName: "LastName",
            isExactMatch: true,
            label: "Last Name",
          },
          {
            doCreate: false,
            doFind: true,
            fieldName: "FirstName",
            isExactMatch: true,
            label: "First Name",
          },
          {
            doCreate: false,
            doFind: true,
            fieldName: "Email",
            isExactMatch: true,
            label: "Email",
          },
        ],
        entityName: "Contact",
        saveToTranscript: "",
      },
    ];
    embedded_svc.init(
      "https://cu.my.salesforce.com",
      "https://cu.my.salesforce-sites.com/BuffInfo",
      gslbBaseURL,
      "00Do0000000Gz4V",
      "Buff_Info_Chat_Team",
      {
        baseLiveAgentContentURL:
          "https://c.la3-c1-ia7.salesforceliveagent.com/content",
        deploymentId: "5722T0000008OsB",
        buttonId: "5732T000000Cb3s",
        baseLiveAgentURL: "https://d.la3-c1-ia7.salesforceliveagent.com/chat",
        eswLiveAgentDevName:
          "EmbeddedServiceLiveAgent_Parent04I2T000000005EUAQ_18603851c77",
        isOfflineSupportEnabled: true,
      }
    );
  }

  function closeEyeCatcher(eyeCatcher) {
    if (eyeCatcher) eyeCatcher.setAttribute("hidden", "");
  }

  function closeEyeCatcherAndBootstrap(eyeCatcher) {
    closeEyeCatcher(eyeCatcher);
    embedded_svc.bootstrapEmbeddedService();
  }

  function addEyeCatcherHtml() {
    const eyeCatcher = document.createElement("div");
    const eyeCatcherCloser = document.createElement("div");
    const eyeCatcherImg = document.createElement("img");

    eyeCatcher.className = "service-servicecloud-eyecatcher";

    eyeCatcherCloser.className = "service-servicecloud-eyecatcher-closer";
    eyeCatcherCloser.addEventListener("click", function () {
      closeEyeCatcher(eyeCatcher);
    });
    eyeCatcherCloser.textContent = "x";

    eyeCatcherImg.className = "service-servicecloud-eyecatcher-img";
    eyeCatcherImg.alt = "Chat now";
    eyeCatcherImg.addEventListener("click", function () {
      closeEyeCatcherAndBootstrap(eyeCatcher);
    });
    eyeCatcherImg.src =
      "https://cdn.livechat-files.com/api/file/lc/main/12416433/0/ec/3cd23a1c1292c884141874e90a228a2b.png";

    eyeCatcher.appendChild(eyeCatcherCloser);
    eyeCatcher.appendChild(eyeCatcherImg);

    body.appendChild(eyeCatcher);
  }
})(window.drupalSettings);
