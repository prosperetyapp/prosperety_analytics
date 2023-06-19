import { register } from "@shopify/web-pixels-extension";

register(({ configuration, analytics, browser, init }) => {
  // Bootstrap and insert pixel script tag here
  analytics.subscribe("page_viewed", (event) => {
    console.log("Page viewed hahah", event);
  });
  const request = fetch(
    "https://prosperety.tritest.link/js/scripts/analytics.js"
  );
  request.then((response) => {
    if (response.status === 200) {
      response.text().then((text) => {
        // Execute the JavaScript code in the external file.
        (function () {
          // This is the same as `fn.doSomething()`.
          {
            /* Now you can use the script */

            console.log("Prosperety Plugin Loaded");
            let prosp_data = [];

            function prosp_tag(key, val) {
              var obj = Object.create(null);
              obj[key] = val;
              prosp_data.push(obj);
            }
            prosp_tag("js_time", Date.now());
            prosp_tag("config", "PROSP-9ul81zLpuFGn9nV");

            const url_win_string = init.context.window.location.href;
            const url_object = new URL(url_win_string);
            const my_param =
              url_object.searchParams.get("username") ?? "shubhamgamey";

            // if (my_param != null) {
            //   //set cookie
            //   setCookie("prosp_username", my_param);
            // }
            function callProsperetySession(type) {
              const payload = {
                brand: "Nike",
                id: my_param,
                action: "action",
                typeOfActivity: type,
              };
              logProsperetySession(payload);
            }
            callProsperetySession("in");
            document.onvisibilitychange = function () {
              if (document.visibilityState === "hidden") {
                callProsperetySession("out");
              }
              if (document.visibilityState === "visible") {
                console.log("Called here");
                callProsperetySession("in");
              }
            };
          }
        })();
      });
    }
  });
});
