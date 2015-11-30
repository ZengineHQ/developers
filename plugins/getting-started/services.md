---
layout: plugin-nav-bar
group: intro
subgroup: services
---
# Plugin Services

Plugin services allow you to run backend code on our servers to communicate with the {{site.productName}} REST API and other systems. Plugin services are specialized [Node.js](https://nodejs.org/){:target="_blank"} apps that receive data from our app and have built-in methods for communicating back with our API. 

Plugin services are meant to act as a go-between from our app and other services. They receive data from the {{site.productName}} app or can fetch additional data from the REST API. Our API library allows you to make API requests as the user running your plugin. For instance, a user could click a button in your frontend plugin that makes a request to your plugin service. Your plugin service can then fetch tasks assigned to that user, send the user an email reminder, then return a successful result back to the user's browser.

## Service Limitations

* Backend services are not supported on Zengine Vault.
* Your zipped service must be under 5MB (including any 3rd party modules)
* 3rd party node modules should ideally be pure Javascript. Native modules or dependencies must be built against [Amazon Linux libraries](https://aws.amazon.com/blogs/compute/nodejs-packages-in-lambda/){:target="_blank"}.  
* The service must consume less than (1.5GB) of memory
* The service should execute quickly and must execute in under 1 minute
* Services are stateless -- there is no session or memory between one request and the next
* Services exit immediately -- if you make HTTP requests, you must wait for them to complete before returning or they will be cancelled