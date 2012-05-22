window.addEvent('domready', function()
{
  
  
  // Try and hide web debug to keep out of the way on dev
  try { sfWebDebugToggleMenu(); } catch(e) { }
  
  // IE fix for errant console.log
  if (typeof console === "undefined") console = { log: function() { } };
});