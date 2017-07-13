var STORE_NAME = "gearsThisBlog";

var MANIFEST_FILENAME = "wp-content/plugins/gears-this-blog/gearsThisBlogManifest.json";

var localServer;
var store;

window.onload = function(){
if(gearsThisBlogCheckGears()) updateStore();
}

function gearsThisBlogInit() {
  if (!window.google || !google.gears) {
	return false;
  } else {
  return google.gears.factory.getPermission(STORE_BLOG_NAME, STORE_MESSAGE_ICON, STORE_MESSAGE);
  }
}

function gearsThisBlogCheckGears() {
  if (!window.google || !google.gears || !google.gears.factory.hasPermission) {
    textOut("<span class='gearsThisBlogError'>You must install Gears first and allow access to the blog to enjoy this.</span>");
	return false;
  } else {
    textOut("<span class='gearsThisBlogValid'>Yeay, Gears is already installed.</span>");
    return true;
  }
}

// Create the managed resource store
function createStore() {
  if (! gearsThisBlogInit()) {
    textOut("<span class='gearsThisBlogError'>You must install Gears first and allow access to the blog.</span>");
    return;
  }

  textOut("<span class='gearsThisBlogLoading'>Check for updates.</span>");

  localServer = google.gears.factory.create("beta.localserver");
  store = localServer.createManagedStore(STORE_NAME);

  if(store.enabled) textOut("<span class='gearsThisBlogLoading'>Updating files.</span>");
  
  store.manifestUrl = MANIFEST_FILENAME;
  store.checkForUpdate();
 
  var timerId = window.setInterval(function() {
    if (store.currentVersion || store.updateStatus == 0) {
      window.clearInterval(timerId);
      textOut("<span class='gearsThisBlogValid'>The blog is now available offline.</span>");
    } else if (store.updateStatus == 3) {
      textOut("<span class='gearsThisBlogError'>Error: " + store.lastErrorMessage + "</span>");
    }
  }, 500);  
}

// Check for updates
function updateStore() {
  if (! gearsThisBlogInit()) {
    textOut("<span class='gearsThisBlogError'>You must install Gears first and allow access to the blog.</span>");
    return;
  }
  
  textOut("<span class='gearsThisBlogLoading'>Check for updates.</span>");

  localServer = google.gears.factory.create("beta.localserver");
  store = localServer.createManagedStore(STORE_NAME);

  if(store.enabled) textOut("<span class='gearsThisBlogLoading'>Updating files.</span>");
  
  store.manifestUrl = MANIFEST_FILENAME;
  store.checkForUpdate();
 
  var timerId = window.setInterval(function() {
    if (store.currentVersion || store.updateStatus == 0) {
      window.clearInterval(timerId);
      textOut("<span class='gearsThisBlogValid'>Files updated. The blog is now available offline.</span>");
    } else if (store.updateStatus == 3) {
      textOut("<span class='gearsThisBlogError'>Error: " + store.lastErrorMessage + "</span>");
    }
  }, 500);  

}

// Remove the managed resource store.
function removeStore() {
  if (! gearsThisBlogInit()) {
    textOut("<span class='gearsThisBlogError'>You must install Gears first and allow access to the blog.</span>");
    return;
  }
  localServer = google.gears.factory.create("beta.localserver");
  localServer.removeManagedStore(STORE_NAME);
  textOut("Data deleted. You will now see the online version of the blog.");
}

// Utility function to output status text.
function textOut(s) {
 var elm = document.getElementById("gearsThisBlogOut");
  elm.innerHTML = s;
}
