---
layout: plugin-nav-bar
group: intro
subgroup: plugin-types
---
# Plugin Types
You can create plugins with several different types: fullPage, inline, recordOverlay, settings, and/or server.  With the exception of the `server` type, you can use registration options to specify which type(s) your plugin will be, as well as related options.  If you do not specify any registration options, the default type is fullPage.

More information about the individual options is available on the [plugin registration options page]({{site.baseurl}}/plugins/registration).

## Full-Page Plugins
Full-page plugins allow you to provide a new screen to the user.  You can specify an icon which will show up in the application's top nav, right along with those for the built-in screens.

To set up a full-page plugin, add an object with `type: 'fullPage'` to the `interfaces` array in your plugin registration.  You may also specify the `icon`, base `route` to be used for the browser's location to navigate to your plugin, as well as custom `routes` to navigate within the plugin.

## Inline Plugins
Inline plugins allow you to add widgets and functionality to specific "injection points" within the base application's code.  For example, a common use case is to add functionality to the record panel.

To set up an inline plugin, add an object with `type: 'inline'` to the `interfaces` array in your plugin registration.  You will also need to specify a `location` parameter on this interface object.  You can find a list of available plugin locations [here]({{site.baseurl}}/plugins/registration/#plugin-locations).

The `route` and `routes` registration options do not apply for inline plugins, and will be ignored if provided. The `icon` option only applies if the plugin is also public.

## Record Overlay
Record overlay plugins allow you to add new tabs to the record panel.

To set up a record overlay plugin, add an object with `type: 'inline'` to the `interfaces` array in your plugin registration.

## Settings

Allows your plugin to add a screen to the workspace settings area. To use plugin settings, add an object with `type: 'inline'` to the `interfaces` array in your plugin registration. 

## Server

Plugin services run on the server. Do not use registration options to add this type. When you add a plugin service, your plugin becomes a server plugin.