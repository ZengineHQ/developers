---
layout: plugin-nav-bar
group: intro
subgroup: publishing
---
# Publishing Plugins
As mentioned in the Getting Started guide, your plugin is available only to you, the developer, until you publish it.  This allows you to develop the plugin without affecting other users.  Publishing your plugin allows you to share it with workspaces that you choose, so that other users in those workspaces may run the plugin.

## Published vs. Draft Code

Our backend stores two separate sets of files for your plugin - the "draft" code that you work with while developing your plugin, and the "published" code that will actually be loaded for end users when they run your plugin.

When you've reached a point where you are ready to release your plugin to be run by other users, you need to publish it.  Publishing copies the current set of draft code over to the published code set.  After this point, your published code will remain unchanged until you choose to publish again.  In the meantime, you can continue working on improvements in your draft code using the developer tools without affecting end users.

## Publishing your plugin

When you are ready to publish your plugin, go to the Developer Tools code editor and click Publishing Settings.  Optionally, you may update your plugin name and description, which can be seen by users with access to the plugin.  Click Save & Publish, and you're done.

### Validation

Note that when you publish your plugin, your JavaScript code will undergo some basic validation checks.  These include making sure the plugin calls the `register` function, passes a syntax check, and prefixes all component names with your chosen plugin namespace.

If any of these checks fail, you will receive a message with the error, and you must fix the problem before you can publish the plugin.  These checks are done only at publishing time to facilitate faster development of your draft plugin.

## Adding your plugin to workspaces

Currently, all plugins are considered "private", and you, as the developer, have control over where they may be used.  Access to your plugins can be added to any workspace in which you are a member.

Once you have published your plugin, the Publishing Settings screen will show you a list of workspaces and allow you to add or remove the plugin to and from workspaces.  Adding a plugin to a workspace allows it to be used, but does not initially "turn it on".  In order for the plugin to be used by workspace members, a workspace administrator must go to the Plugins settings for the workspace and click the "Activate" button for the plugin.

