// You can comment this JS out and the app will still work.
(function() {
  var app = {
    'routes': {
      'about-parku-content': {
        'rendered': function() {
          console.log('view currently showing is "about-parku-content"');
          app.preventScroll();
        }
      },
      'features': {
        'rendered': function() {
          console.log('view currently showing is "features"');
          app.preventScroll();
        }
      },
      'about-us-content': {
        'rendered': function() {
          console.log('view currently showing is "about-us-content"');
          app.preventScroll();
        }
      },
      'contact-content': {
        'rendered': function() {
          console.log('view currently showing is "contact-content"');
          app.preventScroll();
        }
      },
      'gc': {
        'rendered': function() {
          console.log('view currently showing is "gc"');
          app.preventScroll();
        }
      },
      'the-default-view': {
        'rendered': function() {
          console.log('view currently showing is "the-default-view"');
          app.preventScroll();
        }
      },
    },
    'default': 'the-default-view',
    'preventScroll': function() {
      window.scrollTo(0, 0);
    },
    'routeChange': function() {
      app.routeID = location.hash.slice(1);
      app.route = app.routes[app.routeID];
      app.routeElem = document.getElementById(app.routeID);
      app.route.rendered();
    },
    'init': function() {
      window.addEventListener('hashchange', function() {
        app.routeChange();
      });
      if (!window.location.hash) {
        window.location.hash = app.default;
      } else {
        app.routeChange();
      }
    }
  };
  window.app = app;
})();

app.init();

app.preventScroll = () => {
  // document.querySelector('html').scrollTop = document.querySelector(`#${this.target}`).offsetTop;
  // The above line is problematic because `this` refers to the `app` object, not the clicked element.
  // Also, direct scrollTop manipulation on <html> might not always work as expected across browsers.
  // A more standard approach is window.scrollTo, and the target offset can be passed or calculated within the event handler.
  // For now, to simply prevent the jump until proper anchor scrolling is implemented:
  window.scrollTo(0, 0); 
};

app.route = function(){
  let hash = location.hash;
  this.target = hash.slice(1, hash.length);
  this.target = this.target === "" || this.target === "#" ? "the-default-view" : this.target;
  if (this.target !== "features") { // Only prevent scroll if not features, as features might have its own handling or is fine
    this.preventScroll(); // Call the modified preventScroll
  }
  let eles = document.querySelectorAll(".app > section");
  for(let i = 0; i < eles.length; i++){
    eles[i].classList.remove("active");
  }
  document.getElementById(this.target).classList.add("active");
};

app.route();
window.onhashchange = function(){
  app.route();
};

function confirmLogout(event) {
    if (!confirm("Are you sure you want to logout?")) {
        event.preventDefault();
    }
}