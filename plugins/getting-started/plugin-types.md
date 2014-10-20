---
layout: plugin-nav-bar
group: intro
subgroup: plugin-types
---
# Plugins Types
You can create plugins of three different types: fullPage, inline or recordOverlay.  Use registration options to specify which type your plugin will be, as well as related options.  If you do not specify any registration options, the default type is fullPage.

More information about the individual options is available on the [plugin registration options page]({{site.baseurl}}/plugins/registration).

## Full-Page Plugins
Full-page plugins allow you to provide a new screen to the user.  You can specify an icon which will show up in the application's top nav, right along with those for the built-in screens.

To set up a full-page plugin, set `type: 'fullPage'` on your plugin registration.  You may also specify the `icon`, base `route` to be used for the browser's location to navigate to your plugin, as well as custom `routes` to navigate within the plugin. If it is a user-level plugin, you can also specify a `context` in which the plugin should appear (inside a workspace, outside, or both).

## Inline Plugins
Inline plugins allow you to add widgets and functionality to specific "injection points" within the base application's code.  For example, a common use case is to add functionality to the record panel.

To set up an inline plugin, set `type: 'inline'` on your plugin registration.  You will also need to specify a `location` parameter.  You can find a list of available plugin locations [here]({{site.baseurl}}/plugins/registration/#plugin-locations).

The `route`, `routes`, and `context` registration options do not apply for inline plugins, and will be ignored if provided. The `icon` option only applies if the plugin is also public.

## Record Overlay
Record overlay plugins allow you to add new tabs to the record panel.

To set up a record overlay plugin, set `type: 'recordOverlay'` on your plugin registration. 
