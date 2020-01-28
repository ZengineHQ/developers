---
layout: plugin-nav-bar
group: v2-intro
---

# {{site.productName}} Plugin Version 2 Documentation

{{site.productName}} Version 2 plugins remove the need to use our angular 1.2.x code by using Post RPC.
Plugins allow you to extend your {{site.productName}} experience by adding new screens (full screen plugins) or functionality to existing screens (inline plugins) within {{site.productName}}. Plugin services allow you to interact with other systems from the backend. Using our [developer tool]({{site.clientDomain}}/account/developer){:target="_blank"}, you can write Javascript, HTML, and CSS to build out your own custom plugin. From the developer tool, you can also manage and upload any plugin services.

Please note that all development is subject to the [API License Agreement]({{site.marketingDomain}}/terms-of-service/api).

## Migration Guide 

Originally, our plugin system applied plugin code to {{site.productName}} Admin UI by directly injecting valid AngularJS code and CSS into the Admin UI. For a variety of reasons, we at WizeHive felt this was not ideal long term, and so our solution has been to make each plugin an independent application that can run independently from the Admin UI in an iframe. This has two implications for how existing plugins need to be adjusted in order to work in the future:

* Previously plugins had immediate access to certain AngularJS directives or other javascript APIs. Now, those APIs are on the other side of the "iframe wall," and so accessing them requires use of a communication bridge we built over postMessage. This means those APIs require a wrapper that internally makes use of postMessage to retrieve the data, but externally exposes the original API to the plugin code.

* In certain cases, like for many AngularJS directives (UI components), the only way to regain access to these elements is for the directive code to be injected into each plugin (so that it is now present with the plugin code in the iframe). Therefore, this wrapper contains certain pieces of code from the Admin UI application to ensure that plugins have nearly exactly the same look, feel, and functionality before and after a migration.

In order for your legacy plugins to run within an iframe in the new {{site.productName}} plugin system you will need to apply some migrations and alter some code of your plugin.

The Process is defined in [Legacy Plugin Wrapper](https://github.com/ZengineHQ/legacy-plugin-wrapper)

## Legacy Plugins

Do not have time to migrate your plugins? All of your existing plugins will still work and be deployable for the next 6-12 months, but you will not be able to take advantage of the new tech.
If you still need to develop with the Version 1 plugins here is the documentation. [{{site.productName}} Plugins Version 1 Documentation]({{site.baseurl}}/plugins/)

## Example {{site.productName}} Version 2 Plugin

Below are examples of {{site.productName}} Version 2 plugins without the Version 1 dependencies. Bare with us as we build out some more examples.
    
  * [Frontend {{site.productName}} with React ](https://github.com/ZengineHQ/zengine-frontend-plugin-react)
