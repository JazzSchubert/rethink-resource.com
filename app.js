import { Elm } from "Main.elm";

console.log("initializing")
var storageKey = "ReReBlog-Storage";
console.log(storageKey);
var flags = localStorage.getItem(storageKey);
console.log(flags);
var elmApp = Elm.Main.init({flags: flags});

  // TODO:
  // Detect Browser Language on Launch if no data are persisted (template below)
  // Store Language and Session Separately
  // Initialize elm app with either stored language or browser language

  var userLang = navigator.language || navigator.userLanguage; 
  console.log("The language is: " + userLang);

  elmApp.ports.storeCache.subscribe(function(val) {
    if (val === null) {
      localStorage.removeItem(storageKey);
      console.log("removedLocalStorage");
    } else {
      localStorage.setItem(storageKey, JSON.stringify(val));
      console.log(val);
      console.log("Received new key and stored it to browser cash");
    }
    // Report that the new session was stored succesfully.
    setTimeout(function() { elmApp.ports.onStoreChange.send(val); }, 0);
  });

  // Whenever localStorage changes in another tab, report it if necessary.
  window.addEventListener("ReReBlog-Storage", function(event) {
    if (event.storageArea === localStorage && event.key === storageKey) {
      elmApp.ports.onStoreChange.send(event.newValue);
      console.log(event.newValue);
    }
  }, false);