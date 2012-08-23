window.addEvent('domready', function()
{
  // Try and hide web debug to keep out of the way on dev
  try { sfWebDebugToggleMenu(); } catch(e) { }
  
  // IE fix for errant console.log
  if (typeof console === "undefined") console = { log: function() { } };
  
  // Scroll to hide address bar on mobile (iOS / Android) - timeout is necessary
  if ($('wrapper').hasClass('mobile'))
  {
    setTimeout(function() {
      try { window.scrollTo(0, 1); } catch(e) { }
    }, 0);
  }
  
  // ADD PROJECT CODE HERE
});