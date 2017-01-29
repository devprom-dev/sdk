 window.intercomSettings = {
    app_id: "yv4mj125",
    // TODO: The current logged in user's full name
    name: "%name%",
    // TODO: The current logged in user's email address.
    email: "%email%",
    // TODO: The current logged in user's sign-up date as a Unix timestamp.
    created_at: %date%,
    "active_projects": %projects%,
    "active_users": %users%,
    "host_name": "%host%",
     "template_now": "%template_now%",
    "templates": "%templates%",
    "user_role": "%user_role%"
};
 
(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/yv4mj125';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()
